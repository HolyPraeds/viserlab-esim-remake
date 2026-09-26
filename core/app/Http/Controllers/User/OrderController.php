<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController;
use App\Models\Deposit;
use App\Models\GatewayCurrency;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Services\OrderEmailService;
use App\Services\TaurixyService;
use Illuminate\Support\Facades\Http;

class OrderController extends Controller {
    public function pending() {
        $pageTitle = 'Pending Orders';
        $orders = Order::pending()->where('user_id', auth()->id())
            ->with(['orderItem.plan'])
            ->orderBy('id', 'DESC')
            ->paginate(getPaginate());
        return view('Template::user.order.index', compact('pageTitle', 'orders'));
    }

    public function completed() {
        $pageTitle = 'Completed Orders';
        $orders = Order::completed()->where('user_id', auth()->id())
            ->with(['orderItem.plan'])
            ->orderBy('id', 'DESC')
            ->paginate(getPaginate());
        return view('Template::user.order.index', compact('pageTitle', 'orders'));
    }

    public function track(Request $request) {
        $pageTitle = 'Track Order';
        $order = null;
        
        if ($request->has('order_number') && $request->order_number) {
            $orderNumber = $request->order_number;
            
            // Find order by order_number
            $order = Order::where('order_number', $orderNumber)
                ->with(['orderItem.plan', 'user'])
                ->first();
            
            // If user is logged in, ensure they can only see their own orders
            if ($order && auth()->check() && $order->user_id != auth()->id()) {
                $order = null;
            }
        }
        
        return view('Template::user.order.track', compact('pageTitle', 'order'));
    }

    public function payment($id) {
        if (!auth()->check()) {
            return $this->redirectToLoginForPurchase();
        }

        \Log::info('Order payment: received id', ['raw' => $id]);

        // 1) Try resolve by order_number (we now pass order_number in URL)
        $order = Order::where('order_number', (string) $id)->first();
        \Log::info('Order payment: lookup by order_number (raw)', ['found' => (bool) $order]);

        // 2) If not found, try decrypt to numeric id
        if (!$order) {
            try {
                $decrypted = decrypt($id);
                if (ctype_digit((string) $decrypted)) {
                    $order = Order::where('id', (int) $decrypted)->first();
                    \Log::info('Order payment: lookup by decrypted id', ['found' => (bool) $order]);
                }
            } catch (\Throwable $e) {
                \Log::warning('Order decrypt failed (non-fatal)', ['error' => $e->getMessage()]);
            }
        }

        // 3) As a last resort, if the raw token is numeric, treat as id
        if (!$order && ctype_digit((string) $id)) {
            $order = Order::where('id', (int) $id)->first();
            \Log::info('Order payment: lookup by raw numeric id', ['found' => (bool) $order]);
        }
        if (!$order) {
            $notify[] = ['error', 'Invalid order!'];
            return to_route('destination')->withNotify($notify);
        }

        // Filter gateway currencies by payment_currency from order (if set)
        $paymentCurrency = $order->payment_currency ?? 'EUR';
        
        $gatewayCurrency = GatewayCurrency::where(function ($query) use ($order) {
            $query->where('min_amount', '<=', $order->total_amount)->where('max_amount', '>=', $order->total_amount);
        })->where('currency', $paymentCurrency) // Filter by order's payment currency
        ->whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->with('method')->orderBy('name')->get();

        $pageTitle = 'Payment Methods';
        return view('Template::user.order.payment', compact('pageTitle', 'gatewayCurrency',  'order'));
    }



    public function paymentInitiate(Request $request) {
        if (!auth()->check()) {
            return $this->redirectToLoginForPurchase();
        }

        $request->validate([
            'gateway'       => 'required',
            'currency'      => 'required',
            'order_id'      => 'required|integer|exists:orders,id',
        ]);

        $user   = auth()->user();
        // Allow guests: don't require user ownership
        $order = Order::initiated()->with('user', 'orderItem.plan')->find($request->order_id);

        if (!$order) {
            $notify[] = ['error', 'Order not found'];
            return back()->withNotify($notify);
        }

        // for gateway payment
        if ($request->gateway != 'main-balance') {
            $gate = GatewayCurrency::whereHas('method', function ($gate) {
                $gate->where('status', Status::ENABLE);
            })->where('method_code', $request->gateway)
                ->where('currency', $request->currency)
                ->first();

            if (!$gate) {
                $notify[] = ['error', 'Invalid gateway'];
                return back()->withNotify($notify);
            }

            PaymentController::insertDepositData($gate, $order->total_amount, $order->id);
            return to_route('user.deposit.confirm');
        }

        // for wallet payment (only for logged-in users)
        if (!$user) {
            $notify[] = ['error', 'Please login to pay with wallet'];
            return back()->withNotify($notify);
        }

        $walletAmountCredits = $this->convertToCredits((float) $order->total_amount, (string) ($order->payment_currency ?? 'EUR'));
        if ($walletAmountCredits > $user->balance) {
            $notify[] = ['error', 'Insufficient balance'];
            return back()->withNotify($notify);
        }

        $response = dataPlans()->confirmPurchase($order, true);

        if (!$response['status']) {
            $notify[] = ['error', $response['message']];
            return back()->withNotify($notify);
        }

        $notify[] = ['success', 'Order completed successfully'];
        return to_route('user.order.completed')->withNotify($notify);
    }

    // Direct Alp-Pay payment for orders
    public function taurixyDirect(Request $request)
    {
        if (!auth()->check()) {
            return $this->redirectToLoginForPurchase();
        }

        $request->validate([
            'order_id' => 'required|integer|exists:orders,id',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
            'payment_method' => 'nullable|string',
            'country' => 'nullable|string',
            'city' => 'nullable|string',
            'address' => 'nullable|string',
            'zip' => 'nullable|string',
        ]);

        $order = Order::pending()->findOrFail($request->order_id);
        
        // Get payment currency from order (default to EUR if not set)
        $paymentCurrency = $order->payment_currency ?? 'EUR';

        // Create deposit record for this order
        $deposit = new \App\Models\Deposit();
        $deposit->user_id = auth()->id() ?? 0; // 0 for guest
        $deposit->order_id = $order->id;
        $deposit->method_code = 0; // We'll use Alp-Pay
        $deposit->method_currency = $paymentCurrency; // Use payment currency from order
        $deposit->amount = $order->total_amount;
        $deposit->charge = 0; // No processing charge
        $deposit->rate = 1.0;
        $deposit->final_amount = $order->total_amount;
        $deposit->btc_amount = 0;
        $deposit->btc_wallet = "";
        $deposit->trx = getTrx();
        $deposit->success_url = route('user.order.payment.return');
        $deposit->failed_url = route('user.order.payment.return');
        // Store guest contact info so we can send PAYMENT_COMPLETED even when there is no user record
        $deposit->detail = [
            'guest_email'      => $request->email,
            'guest_first_name' => $request->first_name,
            'guest_last_name'  => $request->last_name,
            'guest_phone'      => $request->phone,
            'guest_country'    => $request->country,
            'guest_city'       => $request->city,
        ];
        $deposit->save();

        \Log::info('Order payment deposit created', [
            'order_id' => $order->id,
            'deposit_trx' => $deposit->trx,
            'amount' => $deposit->final_amount,
            'currency' => $paymentCurrency,
        ]);

        // Create Alp-Pay payment
        $baseUrl = config('alppay.base_url');
        $apiKey  = config('alppay.api_key');
        $webhook = config('alppay.webhook_url');

        $configuredReturnUrl = config('alppay.order_return_url');
        $returnUrl = $configuredReturnUrl ?: ($deposit->success_url ?? route('user.order.payment.return'));
        // Safety: avoid legacy success endpoint for AlpPay return handling
        if (is_string($returnUrl) && str_contains($returnUrl, '/payment/success')) {
            $returnUrl = route('user.order.payment.return');
            \Log::warning('AlpPay order_return_url pointed to payment/success, forcing payment/return', [
                'configured_return_url' => $configuredReturnUrl,
                'forced_return_url' => $returnUrl,
            ]);
        }
        \Log::info('AlpPay order returnUrl', [
            'return_url' => $returnUrl,
            'order_number' => $order->order_number,
        ]);
        $payload = [
            'paymentType' => 'DEPOSIT',
            'description' => 'eSIM Order #' . $order->order_number,
            'amount'      => (float) $deposit->final_amount,
            'currency'    => $paymentCurrency, // Use payment currency from order
            'referenceId' => $deposit->trx,
            'webhookUrl'  => $webhook,
            'returnUrl'   => $returnUrl,
            'customer'    => [
                'referenceId' => (string) ($deposit->user_id ?: 'guest'),
                'email'       => $request->email,
                'firstName'   => $request->first_name,
                'lastName'    => $request->last_name,
                'locale'      => app()->getLocale(),
            ],
        ];

        $response = \Illuminate\Support\Facades\Http::withToken($apiKey)
            ->acceptJson()
            ->post(rtrim($baseUrl, '/').'/api/v1/payments', $payload);

        if (!$response->ok()) {
            // Check for 401 Unauthorized (invalid API key)
            if ($response->status() === 401) {
                \Log::error('AlpPay authentication failed (401) - check API key', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                $notify[] = ['error', 'Payment service configuration error. Please contact support.'];
                return redirect()->route('user.deposit.history')->withNotify($notify);
            }

            \Log::error('AlpPay create payment failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            $notify[] = ['error', 'Payment initialization failed'];
            return back()->withNotify($notify);
        }

        $data = $response->json();
        $redirectUrl = $data['result']['redirectUrl'] ?? $data['redirectUrl'] ?? null;
        $paymentId   = $data['result']['id'] ?? $data['id'] ?? null;

        if ($paymentId) {
            $deposit->gateway_trx = $paymentId;
            $deposit->save();
        }

        // So we can find deposit on return when AlpPay doesn't send referenceId in URL
        session()->put('order_payment_deposit_trx', $deposit->trx);
        session()->put('last_order_number', $order->order_number);

        if ($redirectUrl) {
            return redirect()->away($redirectUrl);
        }

        $notify[] = ['error', 'Gateway did not return redirect URL'];
        return back()->withNotify($notify);
    }

    /**
     * Single return URL for AlpPay HPP redirect (success + decline).
     * Reads state from path, query (state, status, payment_status, result, outcome) and normalizes.
     * Reads referenceId from path, query (referenceId, reference_id, ref, trx).
     */
    public function paymentReturn(Request $request, $id = null, $referenceId = null, $state = null, $type = null)
    {
        $rawState = $state ?? $request->route('state') ?? $request->get('state')
            ?? $request->get('status') ?? $request->get('payment_status')
            ?? $request->get('result') ?? $request->get('outcome') ?? '';
        $state = strtoupper(trim((string) $rawState));

        // AlpPay often redirects to .../payment/return/{referenceId} — Laravel puts that in the first path param ({id?}), not {referenceId?}.
        $referenceId = $referenceId ?? $request->route('referenceId') ?? $request->route('id')
            ?? $id
            ?? $request->get('referenceId') ?? $request->get('reference_id')
            ?? $request->get('ref') ?? $request->get('trx')
            ?? session('order_payment_deposit_trx') ?? '';

        \Log::info('AlpPay payment return', [
            'state' => $state,
            'state_raw' => $rawState,
            'referenceId' => $referenceId,
            'path_params' => ['id' => $id, 'referenceId' => $referenceId, 'state' => $state, 'type' => $type],
            'query' => $request->query(),
            'path' => $request->path(),
        ]);

        // Decline/cancel: redirect to payment page to retry
        $declineStates = ['DECLINED', 'CANCELLED', 'FAILED', 'DECLINE', 'CANCEL', 'REJECTED', 'ERROR', 'ABORTED', 'EXPIRED', 'PENDING_FAILED'];
        if (in_array($state, $declineStates)) {
            $deposit = \App\Models\Deposit::where('trx', $referenceId)->first();
            $orderNumber = $deposit && $deposit->order_id
                ? Order::find($deposit->order_id)?->order_number
                : null;
            $notify[] = ['error', 'Payment was declined or cancelled. You can try again.'];
            $url = $orderNumber
                ? route('user.order.payment', $orderNumber)
                : route('home');
            return redirect($url)->withNotify($notify ?? []);
        }

        // Return URL is for showing the right page only. Final state = webhook (COMPLETED/DECLINED/CANCELLED).
        // If we got no state in URL, optionally check API; on return page we now treat
        // everything that is NOT EXPLICITLY COMPLETED as "unsuccessful" for the user
        // (so they clearly see that payment didn't finish), and only COMPLETED as success.
        $declineStatesList = ['DECLINED', 'CANCELLED', 'FAILED', 'DECLINE', 'CANCEL', 'REJECTED', 'ERROR', 'ABORTED', 'EXPIRED', 'PENDING_FAILED', 'PENDING'];

        if ($referenceId) {
            $deposit = Deposit::where('trx', $referenceId)->first();
            if ($deposit && $state === '' && $deposit->gateway_trx) {
                $apiState = $this->fetchAlpPayPaymentState($deposit);
                if ($apiState !== null) {
                    $state = $apiState;
                    \Log::info('AlpPay payment return: state from API (for display only)', ['state' => $state, 'deposit_trx' => $deposit->trx]);
                }
            }
            if ($deposit) {
                session()->forget('order_payment_deposit_trx');

                // If NOT explicitly COMPLETED -> show unsuccessful payment
                if ($state !== 'COMPLETED') {
                    $orderNumber = $deposit->order_id ? Order::find($deposit->order_id)?->order_number : null;
                    $notify[] = ['error', 'Payment was unsuccessful or not completed. Please try again.'];
                    $url = $orderNumber ? route('user.order.payment', $orderNumber) : route('home');
                    return redirect($url)->withNotify($notify ?? []);
                }

                // COMPLETED: webhook should call userDataUpdate; if ngrok/signature failed, apply here once (idempotent)
                $this->applyPaymentCompletionIfWebhookMissed($deposit);
                $this->retryConfirmPurchaseIfOrderStillPending($deposit);

                if ($deposit->order_id) {
                    $notify[] = ['success', 'Payment completed. Check My eSIMs for your profile.'];
                    return redirect()->route('user.esim.active')->withNotify($notify ?? []);
                }
                $notify[] = ['success', 'Deposit completed. Your balance has been updated.'];
                return redirect()->route('user.deposit.history')->withNotify($notify ?? []);
            }
        }

        if ($referenceId === '' && session('order_payment_deposit_trx')) {
            $referenceId = session('order_payment_deposit_trx');
            $deposit = Deposit::where('trx', $referenceId)->first();
            if ($deposit && $deposit->gateway_trx) {
                $apiState = $this->fetchAlpPayPaymentState($deposit);
                session()->forget('order_payment_deposit_trx');

                // If NOT explicitly COMPLETED -> treat as unsuccessful on return page
                if ($apiState !== 'COMPLETED') {
                    $orderNumber = $deposit->order_id ? Order::find($deposit->order_id)?->order_number : null;
                    $notify[] = ['error', 'Payment was unsuccessful or not completed. Please try again.'];
                    $url = $orderNumber ? route('user.order.payment', $orderNumber) : route('home');
                    return redirect($url)->withNotify($notify ?? []);
                }

                $this->applyPaymentCompletionIfWebhookMissed($deposit);
                $this->retryConfirmPurchaseIfOrderStillPending($deposit);

                if ($deposit->order_id) {
                    $notify[] = ['success', 'Payment completed. Check My eSIMs for your profile.'];
                    return redirect()->route('user.esim.active')->withNotify($notify ?? []);
                }
                $notify[] = ['success', 'Deposit completed. Your balance has been updated.'];
                return redirect()->route('user.deposit.history')->withNotify($notify ?? []);
            }
        }

        return $this->paymentSuccess($request);
    }

    /**
     * If AlpPay reports COMPLETED but webhook did not run (wrong signing key, ngrok, etc.),
     * complete the deposit the same way as webhook: balance + order confirmPurchase.
     * Safe if webhook already ran: userDataUpdate only runs for INITIATE/PENDING.
     */
    private function applyPaymentCompletionIfWebhookMissed(Deposit $deposit): void
    {
        $fresh = $deposit->fresh();
        if (!$fresh) {
            return;
        }
        if ($fresh->status != Status::PAYMENT_INITIATE && $fresh->status != Status::PAYMENT_PENDING) {
            \Log::debug('paymentReturn: skip userDataUpdate fallback (deposit not pending)', [
                'trx' => $fresh->trx,
                'status' => $fresh->status,
            ]);
            return;
        }
        try {
            PaymentController::userDataUpdate($fresh);
            \Log::info('paymentReturn: userDataUpdate applied (webhook fallback)', [
                'trx' => $fresh->trx,
                'order_id' => $fresh->order_id,
            ]);
        } catch (\Throwable $e) {
            \Log::error('paymentReturn: userDataUpdate fallback failed', [
                'trx' => $fresh->trx,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * If deposit is already SUCCESS (e.g. webhook ran) but confirmPurchase never completed the order, retry from return URL.
     */
    private function retryConfirmPurchaseIfOrderStillPending(Deposit $deposit): void
    {
        if (!$deposit->order_id) {
            return;
        }
        $order = Order::with('user', 'orderItem.plan')->find($deposit->order_id);
        if (!$order || (int) $order->status === Status::ORDER_COMPLETED) {
            return;
        }
        $dep = $deposit->fresh();
        if (!$dep || (int) $dep->status !== Status::PAYMENT_SUCCESS) {
            return;
        }
        try {
            \Log::info('paymentReturn: retry confirmPurchase (order still pending after payment)', [
                'order_id' => $order->id,
                'trx' => $dep->trx,
            ]);
            dataPlans()->confirmPurchase($order->fresh(['user', 'orderItem.plan']));
        } catch (\Throwable $e) {
            \Log::error('paymentReturn: retry confirmPurchase failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get payment state from AlpPay API (COMPLETED, DECLINED, CANCELLED, PENDING, etc.) or null on error.
     */
    private function fetchAlpPayPaymentState(Deposit $deposit): ?string
    {
        return alppayFetchPaymentState($deposit);
    }

    public function paymentSuccess(Request $request)
    {
        $pageTitle = 'Payment Successful';
        
        // Dev fallback: Check if we have order info in session or request
        // This handles cases where webhook can't reach localhost
        $orderNumber = session('last_order_number') ?? $request->get('order_number');
        
        if ($orderNumber && config('app.env') === 'local') {
            $order = Order::with('user', 'orderItem.plan')->where('order_number', $orderNumber)->where('status', \App\Constants\Status::ORDER_PENDING)->first();
            
            if ($order) {
                // Guard: never complete order from success page when gateway state is not COMPLETED
                $deposit = Deposit::where('order_id', $order->id)->latest('id')->first();
                if ($deposit && $deposit->gateway_trx) {
                    $apiState = $this->fetchAlpPayPaymentState($deposit);
                    if ($apiState !== 'COMPLETED') {
                        $notify[] = ['error', 'Payment was unsuccessful or not completed. Please try again.'];
                        return redirect()->route('user.order.payment', $order->order_number)->withNotify($notify ?? []);
                    }
                }

                \Log::info('Dev fallback: Completing order with eSIM from payment success', [
                    'order_number' => $orderNumber,
                    'order_id' => $order->id
                ]);
                
                // Actually complete order: buy eSIM from provider and create Esim (no wallet deduction)
                $response = dataPlans()->confirmPurchase($order, false);
                if ($response['status']) {
                    $notify[] = ['success', 'Payment completed successfully! Your eSIM is ready in My eSIMs.'];
                    return redirect()->route('user.esim.active')->withNotify($notify ?? []);
                }
                \Log::warning('Dev fallback confirmPurchase failed', ['order_id' => $order->id, 'message' => $response['message'] ?? '']);
                $notify[] = ['warning', 'Payment received. Your eSIM may take a moment to appear—check My eSIMs.'];
            } else {
                $notify[] = ['success', 'Payment completed successfully! Your eSIM will appear in My eSIMs once processing is complete.'];
            }
        } else {
            $notify[] = ['success', 'Payment completed successfully! Your eSIM will appear in My eSIMs once processing is complete.'];
        }
        
        return view('templates.basic.user.order.success', compact('pageTitle'))->withNotify($notify);
    }

    public function payFromWallet(Request $request, $id) {
        if (!auth()->check()) {
            return $this->redirectToLoginForPurchase();
        }

        \Log::info('=== payFromWallet method called ===', [
            'order_id' => $id,
            'user_id' => auth()->id(),
            'user_balance' => auth()->user()->balance,
        ]);

        // Find order by order_number (must be initiated/pending, with orderItem and plan)
        $order = Order::with('user', 'orderItem.plan')->where('order_number', $id)->first();
        
        if (!$order) {
            $notify[] = ['error', 'Order not found'];
            return back()->withNotify($notify);
        }

        $user = auth()->user();
        if ($order->user_id != $user->id) {
            $notify[] = ['error', 'This order does not belong to you'];
            return back()->withNotify($notify);
        }

        if ($order->status == \App\Constants\Status::ORDER_COMPLETED) {
            $notify[] = ['info', 'This order is already paid'];
            return redirect()->route('user.esim.active')->withNotify($notify);
        }

        $walletAmountCredits = $this->convertToCredits((float) $order->total_amount, (string) ($order->payment_currency ?? 'EUR'));
        if ($user->balance < $walletAmountCredits) {
            $notify[] = ['error', 'Insufficient balance in your wallet'];
            return back()->withNotify($notify);
        }

        // Buy eSIM from provider and complete order (deducts balance, creates eSIM, transaction)
        $response = dataPlans()->confirmPurchase($order, true);

        if (!$response['status']) {
            $notify[] = ['error', $response['message'] ?? 'Purchase failed'];
            return back()->withNotify($notify);
        }

        try {
            $this->sendOrderEmails($order->fresh(['user', 'orderItem.plan']));
        } catch (\Throwable $e) {
            \Log::warning('Failed to send order emails from payFromWallet', [
                'order_id' => $order->id ?? null,
                'error'    => $e->getMessage(),
            ]);
        }

        $notify[] = ['success', 'Payment completed successfully! Your eSIM is ready in My eSIMs.'];
        return redirect()->route('user.esim.active')->withNotify($notify);
    }

    /**
     * Centralized helper: send order emails to customer and admin
     * after a payment has been successfully completed.
     */
    protected function sendOrderEmails(Order $order): void
    {
        app(OrderEmailService::class)->sendOrderPlaced($order);
    }

    private function convertToCredits(float $amount, string $currency): float
    {
        $currency = strtoupper(trim($currency));
        return match ($currency) {
            'GBP' => round($amount / 0.87, 2),
            'USD' => round($amount / 1.18, 2),
            default => round($amount, 2),
        };
    }

    private function redirectToLoginForPurchase()
    {
        $notify[] = ['error', 'Please login or register to continue purchase'];
        return redirect()->route('user.login')->withNotify($notify);
    }
}
