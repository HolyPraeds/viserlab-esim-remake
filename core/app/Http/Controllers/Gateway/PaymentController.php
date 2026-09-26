<?php

namespace App\Http\Controllers\Gateway;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Models\AdminNotification;
use App\Models\Deposit;
use App\Models\GatewayCurrency;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller {
    public function deposit() {
        // Показываем только Taurixy gateway
        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE)->where('id', 0);
        })->where('currency', 'EUR')->with('method')->get();
        $pageTitle = 'Add Balance';
        return view('Template::user.payment.deposit', compact('gatewayCurrency', 'pageTitle'));
    }

    public function depositInsert(Request $request) {
        $request->validate([
            'amount'   => 'required|numeric|gt:0',
            'gateway'  => 'required',
        ]);

        $user = auth()->user();
        // Ищем только Taurixy gateway
        $gate = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE)->where('id', 0);
        })->where('currency', 'EUR')->first();

        if (!$gate) {
            $notify[] = ['error', 'Invalid gateway or gateway does not support EUR'];
            return back()->withNotify($notify);
        }

        if ($gate->min_amount > $request->amount || $gate->max_amount < $request->amount) {
            $notify[] = ['error', 'Please follow deposit limit'];
            return back()->withNotify($notify);
        }

        $this->insertDepositData($gate, $request->amount);
        return to_route('user.deposit.confirm');
    }

    public static function insertDepositData($gate, $amount, $orderId = 0) {
        $charge      = $gate->fixed_charge + ($amount * $gate->percent_charge / 100);
        $payable     = $amount + $charge;
        
        // Для EUR не конвертируем, используем сумму как есть
        if ($gate->currency === 'EUR') {
            $finalAmount = $payable;
        } else {
            $finalAmount = $payable * $gate->rate;
        }

        $successUrl = $orderId ? route('user.order.completed') : route('user.deposit.history');

        $data                  = new Deposit();
        $data->user_id         = auth()->id();
        $data->order_id        = $orderId;
        $data->method_code     = $gate->method_code;
        $data->method_currency = strtoupper($gate->currency);
        $data->amount          = $amount;
        $data->charge          = $charge;
        $data->rate            = $gate->rate;
        $data->final_amount    = $finalAmount;
        $data->btc_amount      = 0;
        $data->btc_wallet      = "";
        $data->trx             = getTrx();
        $data->success_url     = $successUrl;
        $data->failed_url      = route('user.deposit.history');
        $data->save();

        session()->put('Track', $data->trx);
    }

    public function depositConfirm() {
        $track   = session()->get('Track');
        $deposit = Deposit::where('trx', $track)->where('status', Status::PAYMENT_INITIATE)->orderBy('id', 'DESC')->with('gateway')->firstOrFail();

        if ($deposit->method_code >= 1000) {
            return to_route('user.deposit.manual.confirm');
        }

        $dirName = $deposit->gateway->alias;
        $new     = __NAMESPACE__ . '\\' . $dirName . '\\ProcessController';

        $data = $new::process($deposit);
        $data = json_decode($data);

        if (isset($data->error)) {
            $notify[] = ['error', $data->message];
            return back()->withNotify($notify);
        }
        if (isset($data->redirect)) {
            return redirect($data->redirect_url);
        }

        if (isset($data->session)) {
            $deposit->btc_wallet = $data->session->id;
            $deposit->save();
        }

        $pageTitle = 'Payment Confirm';
        return view("Template::$data->view", compact('data', 'pageTitle', 'deposit'));
    }

    public static function userDataUpdate($deposit, $isManual = null) {
        $lookupTrx = $deposit->trx ?? null;
        $lookupGatewayTrx = $deposit->gateway_trx ?? null;

        $deposit = Deposit::query()
            ->when($lookupTrx, function ($q) use ($lookupTrx) {
                $q->where('trx', $lookupTrx);
            }, function ($q) use ($deposit, $lookupGatewayTrx) {
                // Fallback for legacy/broken rows where id can be 0.
                if ($lookupGatewayTrx) {
                    $q->where('gateway_trx', $lookupGatewayTrx);
                } else {
                    $q->whereKey($deposit->id);
                }
            })
            ->first();
        if (!$deposit) {
            return;
        }
        if ((int) ($deposit->id ?? 0) === 0) {
            \Log::warning('userDataUpdate: deposit id is 0 (fallback lookup by trx used)', [
                'trx' => $deposit->trx,
                'gateway_trx' => $deposit->gateway_trx,
            ]);
        }

        $status = (int) $deposit->status;
        if ($status !== Status::PAYMENT_INITIATE && $status !== Status::PAYMENT_PENDING) {
            \Log::info('userDataUpdate: skip (deposit not pending)', [
                'trx' => $deposit->trx,
                'status' => $deposit->status,
                'status_int' => $status,
                'order_id' => $deposit->order_id,
            ]);
            return;
        }

        // --- Order card payment: mark paid then fulfill order (no wallet credit).
        // Use integer check: order_id can be string from DB; "0" must stay wallet, not order.
        if ((int) $deposit->order_id !== 0) {
            $deposit->status = Status::PAYMENT_SUCCESS;
            $deposit->save();

            $user = User::find($deposit->user_id);
            $methodName = $deposit->methodName();
            $title = 'Payment successful via ' . $methodName;

            try {
                if (!$isManual && $user) {
                    $adminNotification = new AdminNotification();
                    $adminNotification->user_id = $user->id;
                    $adminNotification->title = $title;
                    $adminNotification->click_url = urlPath('admin.deposit.successful');
                    $adminNotification->save();
                }
            } catch (\Throwable $e) {
                \Log::warning('userDataUpdate: admin notification failed (order payment)', [
                    'deposit_trx' => $deposit->trx,
                    'error' => $e->getMessage(),
                ]);
            }

            $order = Order::with('user', 'orderItem.plan')->find($deposit->order_id);
            if ($order) {
                try {
                    dataPlans()->confirmPurchase($order);
                } catch (\Throwable $e) {
                    \Log::error('userDataUpdate: confirmPurchase failed', [
                        'order_id' => $order->id,
                        'deposit_trx' => $deposit->trx,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
            return;
        }

        // --- Wallet deposit: credit balance and SUCCESS in one DB transaction (avoid SUCCESS without balance).
        $walletCredited = false;
        try {
            DB::transaction(function () use ($deposit, &$walletCredited, $lookupTrx, $lookupGatewayTrx) {
                $d = Deposit::query()
                    ->lockForUpdate()
                    ->when($lookupTrx, function ($q) use ($lookupTrx) {
                        $q->where('trx', $lookupTrx);
                    }, function ($q) use ($deposit, $lookupGatewayTrx) {
                        if ($lookupGatewayTrx) {
                            $q->where('gateway_trx', $lookupGatewayTrx);
                        } else {
                            $q->whereKey($deposit->id);
                        }
                    })
                    ->first();
                if (!$d) {
                    return;
                }
                $ds = (int) $d->status;
                if ($ds !== Status::PAYMENT_INITIATE && $ds !== Status::PAYMENT_PENDING) {
                    return;
                }

                $user = User::lockForUpdate()->find($d->user_id);
                if (!$user) {
                    \Log::error('userDataUpdate: no user for deposit', ['deposit_trx' => $d->trx]);
                    throw new \RuntimeException('user not found for deposit');
                }

                $amountToAdd = (float) $d->amount;
                $paymentCurrency = $d->method_currency ?? 'EUR';
                if ($paymentCurrency !== 'EUR') {
                    $exchangeRates = [
                        'GBP' => 0.87,
                        'USD' => 1.18,
                    ];
                    if (isset($exchangeRates[$paymentCurrency])) {
                        $amountToAdd = (float) $d->amount / $exchangeRates[$paymentCurrency];
                    }
                }

                $user->balance += $amountToAdd;
                $user->save();

                $transaction = new Transaction();
                $transaction->user_id = $d->user_id;
                $transaction->order_id = 0; // schema requires order_id (wallet / top-up is not tied to an order)
                $transaction->amount = $amountToAdd;
                $transaction->post_balance = $user->balance;
                $transaction->charge = $d->charge;
                $transaction->trx_type = '+';
                $transaction->details = 'Deposit by Card';
                $transaction->trx = $d->trx;
                $transaction->remark = 'deposit';
                $transaction->save();

                $d->status = Status::PAYMENT_SUCCESS;
                $d->save();
                $walletCredited = true;
                \Log::info('userDataUpdate: wallet deposit credited', [
                    'user_id' => $user->id,
                    'deposit_trx' => $d->trx,
                    'amount_credits' => $amountToAdd,
                    'post_balance' => $user->balance,
                ]);
            });
        } catch (\Throwable $e) {
            \Log::error('userDataUpdate: wallet deposit transaction failed', [
                'deposit_trx' => $deposit->trx,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return;
        }

        if (!$walletCredited) {
            return;
        }

        $deposit = Deposit::query()
            ->when($lookupTrx, function ($q) use ($lookupTrx) {
                $q->where('trx', $lookupTrx);
            }, function ($q) use ($lookupGatewayTrx, $deposit) {
                if ($lookupGatewayTrx) {
                    $q->where('gateway_trx', $lookupGatewayTrx);
                } else {
                    $q->whereKey($deposit->id);
                }
            })
            ->first();
        $user = User::find($deposit->user_id);
        if (!$deposit || !$user) {
            return;
        }

        $methodName = $deposit->methodName();
        $title = 'Deposit successful via ' . $methodName;
        $creditsRateLine = '1.00 Credits = 1.00 EUR = 0.87 GBP = 1.18 USD';

        $creditsAdded = (float) $deposit->amount;
        $payCur = $deposit->method_currency ?? 'EUR';
        if ($payCur !== 'EUR') {
            $exchangeRates = ['GBP' => 0.87, 'USD' => 1.18];
            if (isset($exchangeRates[$payCur])) {
                $creditsAdded = (float) $deposit->amount / $exchangeRates[$payCur];
            }
        }

        try {
            notify($user, $isManual ? 'DEPOSIT_APPROVE' : 'DEPOSIT_COMPLETE', [
                'fullname' => $user->fullname ?? $user->username ?? 'Customer',
                'method_name' => $methodName,
                'method_currency' => $deposit->method_currency,
                'method_amount' => showAmount($deposit->final_amount, currencyFormat: false),
                'amount' => showAmount($deposit->amount, currencyFormat: false),
                'charge' => showAmount($deposit->charge, currencyFormat: false),
                'rate' => showAmount($deposit->rate, currencyFormat: false),
                'trx' => $deposit->trx,
                'post_balance' => showAmount($user->balance),
                'paid_via' => 'Bank card',
                'deposit_amount' => showAmount($deposit->amount, currencyFormat: false),
                'deposit_currency' => $deposit->method_currency,
                'credits_added' => showAmount($creditsAdded, currencyFormat: false),
                'credits_currency' => 'Credits',
                'credits_rate_line' => $creditsRateLine,
            ]);
        } catch (\Throwable $e) {
            \Log::warning('userDataUpdate: notify DEPOSIT_COMPLETE failed (deposit still credited)', [
                'deposit_trx' => $deposit->trx,
                'error' => $e->getMessage(),
            ]);
        }

        if (!$isManual) {
            try {
                $adminNotification = new AdminNotification();
                $adminNotification->user_id = $user->id;
                $adminNotification->title = $title;
                $adminNotification->click_url = urlPath('admin.deposit.successful');
                $adminNotification->save();
            } catch (\Throwable $e) {
                \Log::warning('userDataUpdate: admin notification failed (deposit)', [
                    'deposit_trx' => $deposit->trx,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    public function manualDepositConfirm() {
        $track = session()->get('Track');
        $data  = Deposit::with('gateway')->where('status', Status::PAYMENT_INITIATE)->where('trx', $track)->first();
        abort_if(!$data, 404);

        if ($data->method_code > 999) {
            if ($data->plan_id) {
                $pageTitle = 'Confirm Payment';
            } else {
                $pageTitle = 'Confirm Deposit';
            }
            $method  = $data->gatewayCurrency();
            $gateway = $method->method;
            return view('Template::user.payment.manual', compact('data', 'pageTitle', 'method', 'gateway'));
        }
        abort(404);
    }

    public function manualDepositUpdate(Request $request) {
        $track = session()->get('Track');
        $data  = Deposit::with('gateway')->where('status', Status::PAYMENT_INITIATE)->where('trx', $track)->first();
        abort_if(!$data, 404);

        $gatewayCurrency = $data->gatewayCurrency();
        $gateway         = $gatewayCurrency->method;
        $formData        = $gateway->form->form_data;

        $formProcessor  = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);
        $request->validate($validationRule);
        $userData = $formProcessor->processFormData($request, $formData);

        $data->detail = $userData;
        $data->status = Status::PAYMENT_PENDING;
        $data->save();

        $adminNotification            = new AdminNotification();
        $adminNotification->user_id   = $data->user->id;
        $adminNotification->title     = 'Deposit request from ' . $data->user->username;
        $adminNotification->click_url = urlPath('admin.deposit.details', $data->id);
        $adminNotification->save();

        if ($data->order_id) {
            $order = Order::initiated()->where('user_id', auth()->id())->find($data->order_id);
            $order->status = Status::ORDER_PENDING;
            $order->save();

            notify($data->user, 'PAYMENT_REQUEST', [
                'order_number'    => $order->order_number,
                'method_name'     => $data->gatewayCurrency()->name,
                'method_currency' => $data->method_currency,
                'method_amount'   => showAmount($data->final_amount, currencyFormat: false),
                'amount'          => showAmount($data->amount, currencyFormat: false),
                'charge'          => showAmount($data->charge, currencyFormat: false),
                'rate'            => showAmount($data->rate, currencyFormat: false),
                'trx'             => $data->trx,
            ]);

            $notify[] = ['success', 'Order payment request has been taken'];
            return to_route('user.order.pending')->withNotify($notify);
        }

        notify($data->user, 'DEPOSIT_REQUEST', [
            'method_name'     => $data->gatewayCurrency()->name,
            'method_currency' => $data->method_currency,
            'method_amount'   => showAmount($data->final_amount, currencyFormat: false),
            'amount'          => showAmount($data->amount, currencyFormat: false),
            'charge'          => showAmount($data->charge, currencyFormat: false),
            'rate'            => showAmount($data->rate, currencyFormat: false),
            'trx'             => $data->trx,
        ]);

        $notify[] = ['success', 'Your deposit request has been taken'];
        return to_route('user.deposit.history')->withNotify($notify);
    }
}