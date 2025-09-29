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

        $deposit = Deposit::where('trx', $request->deposit_trx)->where('status', 0)->firstOrFail();

        $baseUrl = config('alppay.base_url');
        $apiKey  = config('alppay.api_key');
        $webhook = config('alppay.webhook_url');

        $payload = [
            'paymentType' => 'DEPOSIT',
            'description' => 'Deposit via Alp-Pay',
            'amount'      => (float) $deposit->final_amount,
            'currency'    => 'EUR',
            'referenceId' => $deposit->trx,
            'webhookUrl'  => $webhook,
            'customer'    => [
                'referenceId' => (string) $deposit->user_id,
                'email'       => optional($deposit->user)->email,
                'firstName'   => optional($deposit->user)->firstname,
                'lastName'    => optional($deposit->user)->lastname,
                'locale'      => app()->getLocale(),
            ],
        ];

        $response = Http::withToken($apiKey)
            ->acceptJson()
            ->post(rtrim($baseUrl, '/').'/api/v1/payments', $payload);

        if (!$response->ok()) {
            Log::error('AlpPay create payment failed', ['status' => $response->status(), 'body' => $response->body()]);
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
        $paymentId = $payload['id'] ?? null;
        $state     = $payload['state'] ?? null;
        $reference = $payload['referenceId'] ?? null;
        $amount    = $payload['amount'] ?? null;
        $currency  = $payload['currency'] ?? null;

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

        if ($state === 'COMPLETED') {
            if (in_array($deposit->status, [Status::PAYMENT_INITIATE, Status::PAYMENT_PENDING])) {
                // Let core updater set SUCCESS and credit balance
                CorePaymentController::userDataUpdate($deposit);
            }
        } elseif (in_array($state, ['DECLINED', 'CANCELLED'])) {
            if (in_array($deposit->status, [Status::PAYMENT_INITIATE, Status::PAYMENT_PENDING])) {
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
            $res = Http::withToken($apiKey)->acceptJson()->get(rtrim($baseUrl, '/').'/api/v1/payments/'.$deposit->gateway_trx);
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
}