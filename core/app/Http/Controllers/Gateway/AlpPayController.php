<?php

namespace App\Http\Controllers\Gateway;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Constants\Status;
use App\Http\Controllers\Gateway\PaymentController as CorePaymentController;

class AlpPayController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'deposit_trx' => 'required|string',
        ]);

        $deposit = Deposit::where('trx', $request->deposit_trx)->where('status', Status::PAYMENT_INITIATE)->firstOrFail();

        $baseUrl = config('alppay.base_url');
        $apiKey  = config('alppay.api_key');
        $webhook = config('alppay.webhook_url');
        $shopId  = config('alppay.shop_id');

        if (empty($apiKey)) {
            Log::error('AlpPay create: API key is empty (check ALPPAY_API_KEY in .env)');
            $notify[] = ['error', 'Payment is not configured on this environment. Set ALPPAY_API_KEY in .env'];
            return back()->withNotify($notify);
        }

        if (empty($webhook)) {
            Log::warning('AlpPay create: ALPPAY_WEBHOOK_URL is empty — deposits will only complete via return-page polling (GET payment state).');
        }

        $returnUrl = config('alppay.deposit_return_url')
            ?: ($deposit->success_url ?? route('user.deposit.history'));
        Log::info('AlpPay deposit returnUrl', ['return_url' => $returnUrl]);

        $user = $deposit->user;
        $email = $user?->email ? trim((string) $user->email) : '';
        $firstName = trim((string) ($user?->firstname ?? ''));
        $lastName  = trim((string) ($user?->lastname ?? ''));
        if ($firstName === '' && $lastName === '' && !empty($user?->fullname)) {
            $parts = preg_split('/\s+/', trim((string) $user->fullname), 2);
            $firstName = $parts[0] ?? 'Customer';
            $lastName = $parts[1] ?? 'User';
        }
        if ($firstName === '') {
            $firstName = 'Customer';
        }
        if ($lastName === '') {
            $lastName = 'User';
        }
        if ($email === '') {
            Log::error('AlpPay create: user has no email', ['user_id' => $deposit->user_id]);
            $notify[] = ['error', 'Please add an email in your profile before depositing.'];
            return back()->withNotify($notify);
        }

        $payload = [
            'paymentType' => 'DEPOSIT',
            'description' => 'Deposit via Alp-Pay',
            'amount'      => (float) $deposit->final_amount,
            'currency'    => $deposit->method_currency ?? 'EUR',
            'referenceId' => $deposit->trx,
            'webhookUrl'  => $webhook,
            'returnUrl'   => $returnUrl,
            'customer'    => [
                'referenceId' => (string) $deposit->user_id,
                'email'       => $email,
                'firstName'   => $firstName,
                'lastName'    => $lastName,
                'locale'      => app()->getLocale(),
            ],
        ];

        // Add Shop-Id header - REQUIRED for Gateway
        $headers = [];
        if (!empty($shopId)) {
            $headers['Shop-Id'] = $shopId;
        }
        
        $response = Http::withToken($apiKey)
            ->acceptJson()
            ->withHeaders($headers)
            ->post(rtrim($baseUrl, '/').'/api/v1/payments', $payload);

        if (!$response->ok()) {
            Log::error('AlpPay create payment failed', [
                'status' => $response->status(),
                'body' => $response->body(),
                'deposit_trx' => $deposit->trx,
            ]);
            $msg = 'Payment initialization failed';
            if (config('app.debug') && $response->body()) {
                $json = $response->json();
                $msg .= ': ' . (is_array($json) ? ($json['message'] ?? $response->body()) : $response->body());
            }
            $notify[] = ['error', strlen($msg) > 300 ? 'Payment initialization failed (see logs)' : $msg];
            return back()->withNotify($notify);
        }

        $data = $response->json();
        $redirectUrl = $data['result']['redirectUrl'] ?? $data['redirectUrl'] ?? null;
        $paymentId   = $data['result']['id'] ?? $data['id'] ?? data_get($data, 'result.payment.id') ?? null;

        if ($paymentId) {
            $deposit->gateway_trx = $paymentId;
            $deposit->save();
        } else {
            Log::warning('AlpPay create: no payment id in response — polling/fallback may fail', [
                'deposit_trx' => $deposit->trx,
                'response_keys' => is_array($data) ? array_keys($data) : [],
            ]);
        }

        Log::info('AlpPay deposit payment created', [
            'deposit_trx' => $deposit->trx,
            'gateway_trx' => $deposit->gateway_trx,
            'has_redirect' => (bool) $redirectUrl,
        ]);

        if ($redirectUrl) {
            return redirect()->away($redirectUrl);
        }

        $notify[] = ['error', 'Gateway did not return redirect URL'];
        return back()->withNotify($notify);
    }

    public function webhook(Request $request)
    {
        $signature = $request->header('Signature');
        $signingKey = config('alppay.signing_key');
        $raw = $request->getContent();
        $calc = hash_hmac('sha256', $raw, $signingKey);
        if (!hash_equals($calc, (string) $signature)) {
            Log::warning('AlpPay webhook signature mismatch', ['sig' => $signature, 'calc' => $calc]);
            return response()->json(['ok' => false], 401);
        }

        $payload = $request->json()->all();
        // State/id/referenceId may be at top level or under result/payment/data
        $paymentId = $payload['id'] ?? data_get($payload, 'result.id') ?? data_get($payload, 'payment.id') ?? data_get($payload, 'data.id') ?? null;
        $state     = $payload['state'] ?? data_get($payload, 'result.state') ?? data_get($payload, 'payment.state') ?? data_get($payload, 'data.state') ?? null;
        $reference = $payload['referenceId'] ?? data_get($payload, 'result.referenceId') ?? data_get($payload, 'payment.referenceId') ?? data_get($payload, 'data.referenceId') ?? null;
        $amount    = $payload['amount'] ?? data_get($payload, 'result.amount') ?? null;
        $currency  = $payload['currency'] ?? data_get($payload, 'result.currency') ?? null;

        if ($state !== null) {
            $state = strtoupper(trim((string) $state));
        }

        Log::info('AlpPay webhook received', [
            'state' => $state,
            'referenceId' => $reference,
            'paymentId' => $paymentId,
            'payload_keys' => array_keys($payload),
        ]);

        $deposit = null;
        if ($reference) {
            $deposit = Deposit::where('trx', $reference)->first();
        }
        if (!$deposit && $paymentId) {
            $deposit = Deposit::where('gateway_trx', $paymentId)->first();
        }
        if (!$deposit) {
            Log::warning('AlpPay webhook deposit not found', ['reference' => $reference, 'id' => $paymentId]);
            return response()->json(['ok' => true]);
        }

        $depStatus = (int) $deposit->status;
        $depOrderId = (int) $deposit->order_id;
        Log::info('AlpPay webhook deposit resolved', [
            'trx' => $deposit->trx,
            'status' => $depStatus,
            'order_id' => $depOrderId,
            'user_id' => $deposit->user_id,
        ]);

        if ($state === 'COMPLETED') {
            if ($depStatus === Status::PAYMENT_INITIATE || $depStatus === Status::PAYMENT_PENDING) {
                CorePaymentController::userDataUpdate($deposit);
                Log::info('AlpPay webhook: userDataUpdate returned', ['trx' => $deposit->trx]);
            } else {
                Log::info('AlpPay webhook: skip userDataUpdate (deposit not pending)', [
                    'trx' => $deposit->trx,
                    'status' => $depStatus,
                ]);
            }
        } elseif (in_array($state, ['DECLINED', 'CANCELLED', 'FAILED', 'REJECTED', 'ABORTED', 'EXPIRED'])) {
            if ($depStatus === Status::PAYMENT_INITIATE || $depStatus === Status::PAYMENT_PENDING) {
                $deposit->status = Status::PAYMENT_REJECT;
                $deposit->save();
            }
        }

        return response()->json(['ok' => true]);
    }

    // Local fallback: poll status when webhook can't reach localhost
    public function check(Request $request)
    {
        $track = session()->get('Track');
        $deposit = Deposit::where('trx', $track)->firstOrFail();

        if ($deposit->gateway_trx) {
            $baseUrl = config('alppay.base_url');
            $apiKey  = config('alppay.api_key');
            $shopId  = config('alppay.shop_id');
            
            // Add Shop-Id header
            $headers = [];
            if (!empty($shopId)) {
                $headers['Shop-Id'] = $shopId;
            }
            
            $res = Http::withToken($apiKey)
                ->acceptJson()
                ->withHeaders($headers)
                ->get(rtrim($baseUrl, '/').'/api/v1/payments/'.$deposit->gateway_trx);
            if ($res->ok()) {
                $state = data_get($res->json(), 'result.state') ?? data_get($res->json(), 'state');
                if ($state === 'COMPLETED' && in_array($deposit->status, [Status::PAYMENT_INITIATE, Status::PAYMENT_PENDING])) {
                    // Let core updater finalize and credit
                    CorePaymentController::userDataUpdate($deposit);
                    $notify[] = ['success', 'Deposit confirmed'];
                    return to_route('user.deposit.history')->withNotify($notify);
                }
            }
        }
        $notify[] = ['error', 'Payment not completed yet'];
        return back()->withNotify($notify);
    }

    // DEV ONLY: force confirm last tracked deposit (use on localhost)
    public function force(Request $request)
    {
        $track = $request->get('trx') ?: session()->get('Track');
        $deposit = Deposit::where('trx', $track)->firstOrFail();
        if (in_array($deposit->status, [Status::PAYMENT_INITIATE, Status::PAYMENT_PENDING])) {
            CorePaymentController::userDataUpdate($deposit);
            $notify[] = ['success', 'Deposit force-confirmed'];
        } else {
            $notify[] = ['info', 'Deposit already confirmed'];
        }
        return to_route('user.deposit.history')->withNotify($notify);
    }

    /**
     * H2H Flow: Show form for card details first, then create payment with card data
     */
    public function createH2H(Request $request)
    {
        // Get deposit_trx from request (POST) or query string (GET)
        $depositTrx = $request->input('deposit_trx') 
            ?? $request->get('deposit_trx') 
            ?? $request->route('deposit_trx')
            ?? session()->get('Track');
        
        if (!$depositTrx) {
            $notify[] = ['error', 'Deposit transaction ID is required'];
            return redirect()->route('user.deposit.confirm')->withNotify($notify);
        }

        $deposit = Deposit::where('trx', $depositTrx)->where('status', 0)->first();
        
        if (!$deposit) {
            $notify[] = ['error', 'Deposit not found or already processed'];
            return redirect()->route('user.deposit.index')->withNotify($notify);
        }

        // If payment already exists and has card data, go to processing
        if ($deposit->gateway_trx) {
            // Check if payment is already completed or has card data
            $baseUrl = config('alppay.base_url');
            $apiKey  = config('alppay.api_key');
            $paymentId = $deposit->gateway_trx;
            
            $checkResponse = Http::withToken($apiKey)
                ->acceptJson()
                ->get(rtrim($baseUrl, '/').'/api/v1/payments/'.$paymentId);
            
            if ($checkResponse->ok()) {
                $paymentData = $checkResponse->json()['result'] ?? $checkResponse->json();
                $hasCardData = isset($paymentData['paymentMethodDetails']);
                
                if ($hasCardData || in_array($paymentData['state'] ?? null, ['COMPLETED', 'DECLINED', 'CANCELLED'])) {
                    // Payment already processed, go to result page
                    return redirect()->route('user.deposit.alppay.h2h.process', ['deposit_trx' => $deposit->trx]);
                }
            }
            
            // Payment exists but no card data yet, show form
        }
        
        // Show form for card details first
        $pageTitle = 'Enter Payment Details';
        return view('Template::user.deposit.alppay-h2h-form', [
            'deposit' => $deposit,
            'pageTitle' => $pageTitle
        ]);
    }

    /**
     * H2H Flow: Create payment with card details
     */
    public function createH2HWithCard(Request $request)
    {
        $request->validate([
            'deposit_trx' => 'required|string',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'cardholder_name' => 'required|string|max:255',
            'card_number' => 'required|string',
            'expiry_month' => 'required|string|size:2',
            'expiry_year' => 'required|string|size:4',
            'cvv' => 'required|string|min:3|max:4',
            'phone' => 'nullable|string|max:50',
            'address_line1' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'country_code' => 'nullable|string|size:2',
        ]);

        $deposit = Deposit::where('trx', $request->deposit_trx)->where('status', 0)->firstOrFail();

        // If payment already exists, use it
        if ($deposit->gateway_trx) {
            // Use existing payment and just update with card data
            return $this->submitH2H($request);
        }

        $baseUrl = config('alppay.base_url');
        $apiKey  = config('alppay.api_key');
        $webhook = config('alppay.webhook_url');
        $shopId  = config('alppay.shop_id');

        // Validate configuration
        if (empty($apiKey)) {
            Log::error('AlpPay H2H API key is empty');
            $notify[] = ['error', 'Payment service configuration error. API key not set.'];
            return back()->withNotify($notify);
        }

        if (empty($baseUrl)) {
            Log::error('AlpPay H2H base URL is empty');
            $notify[] = ['error', 'Payment service configuration error. Base URL not set.'];
            return back()->withNotify($notify);
        }

        if (empty($webhook)) {
            Log::warning('AlpPay H2H webhook URL is empty - webhooks will not work');
        }

        Log::info('AlpPay H2H configuration', [
            'base_url' => $baseUrl,
            'api_key_set' => !empty($apiKey),
            'webhook_url' => $webhook,
            'shop_id' => $shopId,
            'is_production' => strpos($baseUrl, 'engine.alp-pay.com') !== false
        ]);

        // Generate unique referenceId for this payment attempt
        $uniqueReferenceId = $deposit->trx . '_' . time();

        // Prepare card data - clean and format
        $cardNumber = preg_replace('/[^0-9]/', '', $request->card_number); // Remove all non-numeric characters
        $expiryMonth = str_pad(trim($request->expiry_month), 2, '0', STR_PAD_LEFT);
        $expiryYear = trim($request->expiry_year);
        $cvv = trim($request->cvv);
        
        // Validate card number length (should be 13-19 digits)
        if (strlen($cardNumber) < 13 || strlen($cardNumber) > 19) {
            $notify[] = ['error', 'Invalid card number. Please check and try again.'];
            return back()->withNotify($notify)->withInput();
        }
        
        // Validate expiry month (01-12)
        if ((int)$expiryMonth < 1 || (int)$expiryMonth > 12) {
            $notify[] = ['error', 'Invalid expiry month. Please enter a value between 01 and 12.'];
            return back()->withNotify($notify)->withInput();
        }
        
        // Validate expiry year (should be 4 digits and not in the past)
        if (strlen($expiryYear) !== 4) {
            $notify[] = ['error', 'Invalid expiry year. Please enter a 4-digit year.'];
            return back()->withNotify($notify)->withInput();
        }
        
        // Format phone number according to AlpPay requirements: "country_code local_number" (e.g., "357 123123123")
        // AlpPay requires pattern: \d+ \d+ (digits, space, digits)
        $phone = $request->phone ?? optional($deposit->user)->mobile ?? '';
        $formattedPhone = '';
        
        if (!empty($phone)) {
            // If phone already contains a space, use it as-is (assuming it's already formatted)
            if (strpos($phone, ' ') !== false) {
                // Remove all non-digit and non-space characters, then validate format
                $cleaned = preg_replace('/[^0-9 ]/', '', $phone);
                // Check if it matches pattern: digits space digits
                if (preg_match('/^\d+\s+\d+$/', $cleaned)) {
                    $formattedPhone = $cleaned;
                } else {
                    // Has space but wrong format, reformat
                    $parts = preg_split('/\s+/', $cleaned);
                    $digits = implode('', $parts);
                    $phoneDigits = preg_replace('/[^0-9]/', '', $digits);
                }
            } else {
                // No space, extract digits only
                $phoneDigits = preg_replace('/[^0-9]/', '', $phone);
            }
            
            // If we need to format (no space or wrong format)
            if (empty($formattedPhone) && !empty($phoneDigits)) {
                // Simple formatting: split into country code (1-3 digits) and local number
                // For most cases: use first 1-3 digits as country code, rest as local number
                if (strlen($phoneDigits) >= 8) {
                    // Try to detect country code
                    // Common patterns: 1 (US/CA), 7 (RU/KZ), 20-99 (most countries)
                    $countryCode = '';
                    $localNumber = '';
                    
                    // Check 1-digit codes (1)
                    if (strlen($phoneDigits) >= 10 && substr($phoneDigits, 0, 1) === '1') {
                        $countryCode = '1';
                        $localNumber = substr($phoneDigits, 1);
                    }
                    // Check 2-digit codes (20-99)
                    elseif (strlen($phoneDigits) >= 9) {
                        $code2 = substr($phoneDigits, 0, 2);
                        if ((int)$code2 >= 20 && (int)$code2 <= 99) {
                            $countryCode = $code2;
                            $localNumber = substr($phoneDigits, 2);
                        }
                    }
                    
                    // If no country code detected, use first 2-3 digits as code
                    if (empty($countryCode)) {
                        $splitPoint = min(3, max(1, floor(strlen($phoneDigits) / 2)));
                        $countryCode = substr($phoneDigits, 0, $splitPoint);
                        $localNumber = substr($phoneDigits, $splitPoint);
                    }
                    
                    if (!empty($countryCode) && !empty($localNumber)) {
                        $formattedPhone = $countryCode . ' ' . $localNumber;
                    }
                } else {
                    // Too short, use default format with space in middle
                    $midPoint = max(1, floor(strlen($phoneDigits) / 2));
                    $formattedPhone = substr($phoneDigits, 0, $midPoint) . ' ' . substr($phoneDigits, $midPoint);
                }
            }
        }
        
        // Create payment for H2H flow with card data
        // Get customer IP and convert IPv6 to IPv4 if needed (CardAQ requires IPv4)
        $customerIp = $request->ip();
        // Try to get IPv4 from headers if current IP is IPv6
        if (filter_var($customerIp, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            // Try to get IPv4 from X-Forwarded-For or X-Real-IP headers
            $forwardedFor = $request->header('X-Forwarded-For');
            if ($forwardedFor) {
                $ips = explode(',', $forwardedFor);
                foreach ($ips as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                        $customerIp = $ip;
                        break;
                    }
                }
            }
            // If still IPv6, try X-Real-IP
            if (filter_var($customerIp, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
                $realIp = $request->header('X-Real-IP');
                if ($realIp && filter_var($realIp, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                    $customerIp = $realIp;
                }
            }
        }
        
        // Ensure phone is always sent (not null) - use default if empty
        $phoneToSend = !empty($formattedPhone) ? $formattedPhone : '000 0000000';
        
        // Get returnUrl from deposit success_url, fallback to route
        $returnUrl = $deposit->success_url ?? route('user.deposit.history');
        
        $payload = [
            'paymentType' => 'DEPOSIT',
            'paymentMethod' => 'BASIC_CARD', // Explicitly set payment method for H2H
            'description' => 'Deposit via Alp-Pay H2H',
            'amount'      => (float) $deposit->final_amount,
            'currency'    => $deposit->method_currency ?? 'EUR',
            'referenceId' => $uniqueReferenceId,
            'webhookUrl'  => $webhook,
            'returnUrl'   => $returnUrl, // Added: Required for Gateway to create proper redirect URLs in CardAQ
            'customer'    => array_filter([
                'referenceId' => (string) $deposit->user_id,
                'email'       => $request->email,
                'firstName'   => $request->first_name,
                'lastName'    => $request->last_name,
                'phone'       => $phoneToSend, // Always send phone (not null)
                'ip'          => $customerIp, // IP address in customer object for H2H (IPv4 preferred)
                'locale'      => app()->getLocale(),
            ], function($value) {
                return $value !== null && $value !== '';
            }),
            // Card data for H2H - MUST be included in POST request
            'card' => [
                'cardNumber' => $cardNumber,
                'cardholderName' => trim($request->cardholder_name), // lowercase 'c' - Gateway is case-sensitive!
                'cardSecurityCode' => $cvv,
                'expiryMonth' => $expiryMonth,
                'expiryYear' => $expiryYear,
            ],
        ];

        // Always add billing address (at least with countryCode) - CardAQ may require it for AVS
        $payload['billingAddress'] = [
            'addressLine1' => $request->address_line1 ?? '',
            'city' => $request->city ?? '',
            'postalCode' => $request->postal_code ?? '',
            'countryCode' => strtoupper($request->country_code ?? 'US'), // Default to US if not provided
        ];
        
        // Remove empty billing address fields
        $payload['billingAddress'] = array_filter($payload['billingAddress'], function($value) {
            return $value !== null && $value !== '';
        });
        
        // If billingAddress is empty after filtering, keep at least countryCode
        if (empty($payload['billingAddress'])) {
            $payload['billingAddress'] = [
                'countryCode' => strtoupper($request->country_code ?? 'US'),
            ];
        }

        Log::info('AlpPay H2H creating payment with card data', [
            'deposit_trx' => $deposit->trx,
            'unique_reference_id' => $uniqueReferenceId,
            'amount' => $deposit->final_amount,
            'currency' => $deposit->method_currency,
            'webhook_url' => $webhook,
            'return_url' => $returnUrl,
            'base_url' => $baseUrl,
            'has_card_data' => true,
            'customer_ip' => $customerIp,
            'customer_ip_type' => filter_var($customerIp, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ? 'IPv6' : 'IPv4',
            'phone_sent' => $phoneToSend,
            'billing_address' => $payload['billingAddress'] ?? null,
        ]);

        // Log full request details before sending
        Log::info('AlpPay H2H request details', [
            'url' => rtrim($baseUrl, '/').'/api/v1/payments',
            'api_key_length' => strlen($apiKey),
            'api_key_preview' => substr($apiKey, 0, 10) . '...',
            'shop_id' => $shopId,
            'shop_id_in_header' => !empty($shopId),
            'payload' => $payload,
            'webhook_url' => $webhook,
            'is_sandbox' => strpos($baseUrl, 'sandbox') !== false,
            'is_production' => strpos($baseUrl, 'engine.alp-pay.com') !== false && strpos($baseUrl, 'sandbox') === false
        ]);

        // Add Shop-Id header - REQUIRED for Gateway to select correct terminal
        $headers = [];
        if (!empty($shopId)) {
            $headers['Shop-Id'] = $shopId;
        }
        
        // Log headers being sent
        Log::info('AlpPay H2H request headers', [
            'shop_id' => $shopId,
            'headers' => $headers,
            'has_shop_id_header' => !empty($headers['Shop-Id'])
        ]);
        
        $response = Http::withToken($apiKey)
            ->acceptJson()
            ->withHeaders($headers)
            ->timeout(30)
            ->post(rtrim($baseUrl, '/').'/api/v1/payments', $payload);

        // Log full response
        Log::info('AlpPay H2H full response', [
            'status' => $response->status(),
            'headers' => $response->headers(),
            'body' => $response->body(),
            'json' => $response->json()
        ]);

        if (!$response->ok()) {
            $errorBody = $response->body();
            $errorJson = $response->json();
            
            // Handle 409 CONFLICT - should not happen with unique referenceId, but handle just in case
            if ($response->status() === 409) {
                Log::error('AlpPay H2H payment conflict (409) - DETAILED DEBUG', [
                    'deposit_trx' => $deposit->trx,
                    'unique_reference_id' => $uniqueReferenceId,
                    'payload_referenceId' => $payload['referenceId'],
                    'full_response_body' => $errorBody,
                    'response_json' => $errorJson,
                    'response_headers' => $response->headers(),
                    'api_url' => rtrim($baseUrl, '/').'/api/v1/payments'
                ]);
                
                // Try to get more details from error response
                $errorDetails = '';
                if (isset($errorJson['errors'])) {
                    $errorDetails = json_encode($errorJson['errors'], JSON_PRETTY_PRINT);
                } elseif (isset($errorJson['message'])) {
                    $errorDetails = $errorJson['message'];
                } elseif (isset($errorJson['error'])) {
                    $errorDetails = $errorJson['error'];
                }
                
                $errorMessage = 'Payment conflict (409). ' . ($errorDetails ?: 'Reference ID may already exist. Please check logs for details.');
                $notify[] = ['error', $errorMessage];
                return back()->withNotify($notify);
            }
            
            Log::error('AlpPay H2H create payment failed', [
                'status' => $response->status(),
                'body' => $errorBody,
                'json' => $errorJson,
                'payload' => $payload,
                'headers' => $response->headers()
            ]);
            
            // Extract detailed error message
            $errorMessage = 'Payment initialization failed';
            $errorDetails = [];
            
            if (isset($errorJson['message'])) {
                $errorMessage = is_array($errorJson['message']) 
                    ? json_encode($errorJson['message']) 
                    : (string) $errorJson['message'];
            } elseif (isset($errorJson['error'])) {
                $errorMessage = is_array($errorJson['error']) 
                    ? json_encode($errorJson['error']) 
                    : (string) $errorJson['error'];
            }
            
            // Extract validation errors if present
            if (isset($errorJson['errors']) && is_array($errorJson['errors'])) {
                foreach ($errorJson['errors'] as $field => $messages) {
                    if (is_array($messages)) {
                        // Handle array of messages
                        $messageStrings = [];
                        foreach ($messages as $msg) {
                            if (is_array($msg)) {
                                $messageStrings[] = json_encode($msg);
                            } else {
                                $messageStrings[] = (string) $msg;
                            }
                        }
                        $errorDetails[] = $field . ': ' . implode(', ', $messageStrings);
                    } else {
                        $errorDetails[] = $field . ': ' . (string) $messages;
                    }
                }
            }
            
            // Log full error details for debugging
            Log::error('AlpPay H2H validation error details', [
                'error_message' => $errorMessage,
                'error_details' => $errorDetails,
                'full_error_json' => $errorJson,
                'sent_payload' => $payload
            ]);
            
            // Build user-friendly error message
            $userMessage = $errorMessage;
            if (!empty($errorDetails)) {
                $userMessage .= ' (' . implode('; ', $errorDetails) . ')';
            }
            
            $notify[] = ['error', 'Payment initialization failed: ' . $userMessage . ' (Status: ' . $response->status() . ')'];
            return back()->withNotify($notify)->withInput();
        }

        $data = $response->json();
        $paymentId = $data['result']['id'] ?? $data['id'] ?? null;

        Log::info('AlpPay H2H payment response', [
            'deposit_trx' => $deposit->trx,
            'unique_reference_id' => $uniqueReferenceId,
            'status_code' => $response->status(),
            'response_data' => $data,
            'payment_id' => $paymentId,
            'full_response' => $response->body()
        ]);

        if (!$paymentId) {
            Log::error('AlpPay H2H payment ID not found', ['response' => $data]);
            $notify[] = ['error', 'Payment ID not received from gateway'];
            return back()->withNotify($notify);
        }

        // Save payment ID to deposit
        $deposit->gateway_trx = $paymentId;
        $deposit->save();

        Log::info('AlpPay H2H payment created successfully', [
            'deposit_trx' => $deposit->trx,
            'payment_id' => $paymentId,
            'gateway_trx_saved' => $deposit->gateway_trx
        ]);

        // 3DS: redirect to redirectUrl or first externalRefs[].url (e.g. 3DS challenge)
        $redirectOr3ds = $this->getRedirectOr3dsUrl($data);
        if ($redirectOr3ds) {
            Log::info('AlpPay H2H redirect to 3DS/redirectUrl', ['url' => $redirectOr3ds, 'deposit_trx' => $deposit->trx]);
            return redirect()->away($redirectOr3ds);
        }

        return redirect()->route('user.deposit.alppay.h2h.process', ['deposit_trx' => $deposit->trx]);
    }

    /**
     * H2H Flow: Process payment with customer IP
     */
    public function processH2H(Request $request)
    {
        $request->validate([
            'deposit_trx' => 'required|string',
        ]);

        $deposit = Deposit::where('trx', $request->deposit_trx)->first();
        
        if (!$deposit) {
            $notify[] = ['error', 'Deposit not found. Please create a deposit first.'];
            return redirect()->route('user.deposit.index')->withNotify($notify);
        }

        if (!$deposit->gateway_trx) {
            $notify[] = ['error', 'Payment not initialized. Please click "Pay by Card (H2H Flow)" button first.'];
            return redirect()->route('user.deposit.confirm')->withNotify($notify);
        }

        $baseUrl = config('alppay.base_url');
        $apiKey  = config('alppay.api_key');
        $shopId  = config('alppay.shop_id');
        $paymentId = $deposit->gateway_trx;

        // Get customer IP
        $customerIp = $request->ip();
        if ($request->has('customer_ip')) {
            $customerIp = $request->input('customer_ip');
        }

        // PATCH request with customer IP
        $patchPayload = [
            'customerIp' => $customerIp
        ];

        // Add Shop-Id header
        $headers = [];
        if (!empty($shopId)) {
            $headers['Shop-Id'] = $shopId;
        }

        $response = Http::withToken($apiKey)
            ->acceptJson()
            ->withHeaders($headers)
            ->patch(rtrim($baseUrl, '/').'/api/v1/payments/'.$paymentId, $patchPayload);

        if (!$response->ok()) {
            Log::error('AlpPay H2H PATCH failed', [
                'status' => $response->status(),
                'body' => $response->body(),
                'payment_id' => $paymentId
            ]);
            
            $errorData = $response->json();
            $errorMessage = $errorData['message'] ?? $errorData['error'] ?? 'Failed to process payment';
            
            $notify[] = ['error', 'Failed to process payment: ' . $errorMessage];
            return redirect()->route('user.deposit.confirm')->withNotify($notify);
        }

        $data = $response->json();
        $paymentData = $data['result'] ?? $data;

        // Extract externalRefs for customer instructions
        $externalRefs = $paymentData['externalRefs'] ?? [];
        $state = $paymentData['state'] ?? null;

        Log::info('AlpPay H2H payment processed (IP sent)', [
            'payment_id' => $paymentId,
            'state' => $state,
            'external_refs' => $externalRefs
        ]);

        // In H2H flow, after sending IP, we should show the form for card details
        // Even if state is DECLINED at this point (before card data is sent),
        // we need to show the form. Only show final result if payment is COMPLETED
        // or if card data was already submitted (we'll track this with a flag)
        
        // 3DS: redirect to redirectUrl or first externalRefs[].url (e.g. 3DS challenge)
        $redirectOr3ds = $this->getRedirectOr3dsUrl($data);
        if ($redirectOr3ds) {
            Log::info('AlpPay H2H processH2H redirect to 3DS', ['url' => $redirectOr3ds, 'deposit_trx' => $deposit->trx]);
            return redirect()->away($redirectOr3ds);
        }

        $cardSubmitted = isset($paymentData['paymentMethodDetails']);
        $pageTitle = 'Payment Instructions';
        return view('Template::user.deposit.alppay-h2h', [
            'deposit' => $deposit,
            'paymentId' => $paymentId,
            'externalRefs' => $externalRefs,
            'state' => ($cardSubmitted || $state === 'COMPLETED') ? $state : null,
            'customerIp' => $customerIp,
            'pageTitle' => $pageTitle,
            'cardSubmitted' => $cardSubmitted
        ]);
    }

    /**
     * H2H Flow: Submit card details
     */
    public function submitH2H(Request $request)
    {
        $request->validate([
            'deposit_trx' => 'required|string',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'cardholder_name' => 'required|string|max:255',
            'card_number' => 'required|string',
            'expiry_month' => 'required|string|size:2',
            'expiry_year' => 'required|string|size:4',
            'cvv' => 'required|string|min:3|max:4',
            'phone' => 'nullable|string|max:50',
            'address_line1' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'country_code' => 'nullable|string|size:2',
        ]);

        $deposit = Deposit::where('trx', $request->deposit_trx)->firstOrFail();

        if (!$deposit->gateway_trx) {
            $notify[] = ['error', 'Payment not initialized'];
            return redirect()->route('user.deposit.confirm')->withNotify($notify);
        }

        $baseUrl = config('alppay.base_url');
        $apiKey  = config('alppay.api_key');
        $shopId  = config('alppay.shop_id');
        $paymentId = $deposit->gateway_trx;

        // Prepare card data
        $cardNumber = str_replace(' ', '', $request->card_number);
        
        // Prepare PATCH payload with card details
        $patchPayload = [
            'customerIp' => $request->ip(),
            'paymentMethod' => 'BASIC_CARD',
            'paymentMethodDetails' => [
                'customerAccountNumber' => $cardNumber,
                'cardholderName' => $request->cardholder_name, // lowercase 'c' - Gateway is case-sensitive!
                'cardExpiryMonth' => str_pad($request->expiry_month, 2, '0', STR_PAD_LEFT),
                'cardExpiryYear' => $request->expiry_year,
                'cardCvv' => $request->cvv,
            ],
            'customer' => [
                'firstName' => $request->first_name,
                'lastName' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone ?? optional($deposit->user)->mobile ?? '',
            ]
        ];

        // Add billing address if provided
        if ($request->address_line1 || $request->city || $request->postal_code || $request->country_code) {
            $patchPayload['billingAddress'] = [];
            if ($request->address_line1) {
                $patchPayload['billingAddress']['addressLine1'] = $request->address_line1;
            }
            if ($request->city) {
                $patchPayload['billingAddress']['city'] = $request->city;
            }
            if ($request->postal_code) {
                $patchPayload['billingAddress']['postalCode'] = $request->postal_code;
            }
            if ($request->country_code) {
                $patchPayload['billingAddress']['countryCode'] = strtoupper($request->country_code);
            }
        }

        // Add Shop-Id header
        $headers = [];
        if (!empty($shopId)) {
            $headers['Shop-Id'] = $shopId;
        }

        $response = Http::withToken($apiKey)
            ->acceptJson()
            ->withHeaders($headers)
            ->patch(rtrim($baseUrl, '/').'/api/v1/payments/'.$paymentId, $patchPayload);

        if (!$response->ok()) {
            Log::error('AlpPay H2H submit card failed', [
                'status' => $response->status(),
                'body' => $response->body(),
                'payment_id' => $paymentId
            ]);
            
            $errorData = $response->json();
            $errorMessage = $errorData['message'] ?? $errorData['error'] ?? 'Failed to process payment';
            
            $notify[] = ['error', $errorMessage];
            return back()->withInput()->withNotify($notify);
        }

        $data = $response->json();
        $paymentData = $data['result'] ?? $data;
        $state = $paymentData['state'] ?? null;
        $externalRefs = $paymentData['externalRefs'] ?? [];

        Log::info('AlpPay H2H card submitted', [
            'payment_id' => $paymentId,
            'state' => $state
        ]);

        // If payment completed, update deposit
        if ($state === 'COMPLETED' && in_array($deposit->status, [Status::PAYMENT_INITIATE, Status::PAYMENT_PENDING])) {
            CorePaymentController::userDataUpdate($deposit);
            $notify[] = ['success', 'Payment completed successfully'];
            return redirect()->route('user.deposit.history')->withNotify($notify);
        } elseif (in_array($state, ['DECLINED', 'CANCELLED']) && in_array($deposit->status, [Status::PAYMENT_INITIATE, Status::PAYMENT_PENDING])) {
            $deposit->status = Status::PAYMENT_REJECT;
            $deposit->save();
            $notify[] = ['error', 'Payment was ' . strtolower($state)];
        }

        // 3DS: redirect to redirectUrl or first externalRefs[].url
        $redirectOr3ds = $this->getRedirectOr3dsUrl($data);
        if ($redirectOr3ds) {
            Log::info('AlpPay H2H submitH2H redirect to 3DS', ['url' => $redirectOr3ds, 'deposit_trx' => $deposit->trx]);
            return redirect()->away($redirectOr3ds);
        }

        $pageTitle = 'Payment Instructions';
        return view('Template::user.deposit.alppay-h2h', [
            'deposit' => $deposit,
            'paymentId' => $paymentId,
            'externalRefs' => $externalRefs,
            'state' => $state,
            'customerIp' => $request->ip(),
            'pageTitle' => $pageTitle
        ]);
    }

    /**
     * H2H Flow: Check payment status and refresh instructions
     */
    public function checkH2H(Request $request)
    {
        $request->validate([
            'deposit_trx' => 'required|string',
        ]);

        $deposit = Deposit::where('trx', $request->deposit_trx)->firstOrFail();

        if (!$deposit->gateway_trx) {
            $notify[] = ['error', 'Payment not initialized'];
            return redirect()->route('user.deposit.confirm')->withNotify($notify);
        }

        $baseUrl = config('alppay.base_url');
        $apiKey  = config('alppay.api_key');
        $paymentId = $deposit->gateway_trx;

        // GET payment status
        // Add Shop-Id header for GET request
        $shopId = config('alppay.shop_id');
        $headers = [];
        if (!empty($shopId)) {
            $headers['Shop-Id'] = $shopId;
        }
        
        $response = Http::withToken($apiKey)
            ->acceptJson()
            ->withHeaders($headers)
            ->get(rtrim($baseUrl, '/').'/api/v1/payments/'.$paymentId);

        if (!$response->ok()) {
            Log::error('AlpPay H2H GET status failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            $notify[] = ['error', 'Failed to check payment status'];
            return back()->withNotify($notify);
        }

        $data = $response->json();
        $paymentData = $data['result'] ?? $data;
        $state = $paymentData['state'] ?? null;
        $externalRefs = $paymentData['externalRefs'] ?? [];

        // Update deposit status if payment completed
        if ($state === 'COMPLETED' && in_array($deposit->status, [Status::PAYMENT_INITIATE, Status::PAYMENT_PENDING])) {
            CorePaymentController::userDataUpdate($deposit);
            $notify[] = ['success', 'Payment completed successfully'];
            return redirect()->route('user.deposit.history')->withNotify($notify);
        } elseif (in_array($state, ['DECLINED', 'CANCELLED']) && in_array($deposit->status, [Status::PAYMENT_INITIATE, Status::PAYMENT_PENDING])) {
            $deposit->status = Status::PAYMENT_REJECT;
            $deposit->save();
            $notify[] = ['error', 'Payment was ' . strtolower($state)];
            return redirect()->route('user.deposit.history')->withNotify($notify);
        }

        // 3DS: redirect to redirectUrl or first externalRefs[].url
        $redirectOr3ds = $this->getRedirectOr3dsUrl($data);
        if ($redirectOr3ds) {
            Log::info('AlpPay H2H checkH2H redirect to 3DS', ['url' => $redirectOr3ds, 'deposit_trx' => $deposit->trx]);
            return redirect()->away($redirectOr3ds);
        }

        $pageTitle = 'Payment Instructions';
        $cardSubmitted = isset($paymentData['paymentMethodDetails']);
        return view('Template::user.deposit.alppay-h2h', [
            'deposit' => $deposit,
            'paymentId' => $paymentId,
            'externalRefs' => $externalRefs,
            'state' => $state,
            'customerIp' => $request->ip(),
            'pageTitle' => $pageTitle,
            'cardSubmitted' => $cardSubmitted
        ]);
    }

    /**
     * Get redirect/3DS URL from AlpPay response: redirectUrl or first externalRefs[].url
     */
    private function getRedirectOr3dsUrl(array $data): ?string
    {
        $paymentData = $data['result'] ?? $data;
        $url = $paymentData['redirectUrl'] ?? $data['redirectUrl'] ?? null;
        if ($url && is_string($url) && preg_match('#^https?://#', $url)) {
            return $url;
        }
        $refs = $paymentData['externalRefs'] ?? [];
        if (is_array($refs)) {
            foreach ($refs as $ref) {
                if (is_array($ref) && !empty($ref['url']) && is_string($ref['url']) && preg_match('#^https?://#', $ref['url'])) {
                    return $ref['url'];
                }
            }
        }
        return null;
    }
}