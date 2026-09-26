<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\GatewayCurrency;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Plan;
use App\Models\Order;
use Illuminate\Http\Request;

class DepositController extends Controller
{
    public function index()
    {
        finalizePendingAlpPayDepositsForUser(auth()->user());

        $pageTitle = 'Deposit History';
        $deposits = auth()->user()->deposits()->searchable(['trx'])->with(['gateway'])->orderBy('id', 'desc')->paginate(getPaginate());
        return view('Template::user.deposit.index', compact('pageTitle', 'deposits'));
    }

    public function deposit()
    {
        // Убеждаемся, что все три валюты существуют и имеют правильные лимиты 1-2800
        $method = \App\Models\Gateway::where('code', 0)->where('status', Status::ENABLE)->first();
        if (!$method) {
            $notify[] = ['error', 'Payment method not available'];
            return back()->withNotify($notify);
        }
        
        $currencies = [
            ['currency' => 'EUR', 'symbol' => '€', 'name' => 'Euro'],
            ['currency' => 'GBP', 'symbol' => '£', 'name' => 'British Pound'],
            ['currency' => 'USD', 'symbol' => '$', 'name' => 'US Dollar'],
        ];
        
        foreach ($currencies as $curr) {
            GatewayCurrency::updateOrCreate(
                [
                    'method_code' => 0,
                    'currency' => $curr['currency']
                ],
                [
                    'name' => $curr['name'],
                    'symbol' => $curr['symbol'],
                    'gateway_alias' => 'taurixy',
                    'min_amount' => 1,
                    'max_amount' => 2800,
                    'percent_charge' => 0,
                    'fixed_charge' => 0,
                    'rate' => 1,
                ]
            );
        }
        
        // Получаем все валюты с правильным порядком (EUR, GBP, USD) и обновляем лимиты, если нужно
        $gatewayCurrencies = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE)->where('code', 0);
        })->whereIn('currency', ['EUR', 'GBP', 'USD'])
        ->with('method')
        ->orderByRaw("FIELD(currency, 'EUR', 'GBP', 'USD')")
        ->get();
        
        // Обновляем лимиты для всех записей до 1-2800
        foreach ($gatewayCurrencies as $gc) {
            if ($gc->min_amount != 1 || $gc->max_amount != 2800) {
                $gc->min_amount = 1;
                $gc->max_amount = 2800;
                $gc->save();
            }
        }

        if ($gatewayCurrencies->isEmpty()) {
            $notify[] = ['error', 'Payment method not available'];
            return back()->withNotify($notify);
        }

        $pageTitle = 'Add Balance';
        return view('Template::user.payment.deposit', compact('gatewayCurrencies', 'pageTitle'));
    }

    public function depositInsert(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|gt:0',
            'currency' => 'required|in:EUR,GBP,USD'
        ]);

        $user = auth()->user();
        $selectedCurrency = $request->input('currency', 'EUR');
        
        // Получаем gateway для выбранной валюты
        $gate = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE)->where('code', 0);
        })->where('currency', $selectedCurrency)->first();

        if (!$gate) {
            $notify[] = ['error', 'Payment method not available for ' . $selectedCurrency];
            return back()->withNotify($notify);
        }

        // Устанавливаем лимиты, если они неправильные
        if ($gate->min_amount != 1 || $gate->max_amount != 2800) {
            $gate->min_amount = 1;
            $gate->max_amount = 2800;
            $gate->save();
        }

        if ($gate->min_amount > $request->amount || $gate->max_amount < $request->amount) {
            $notify[] = ['error', 'Please follow deposit limit (1 - 2800 ' . $selectedCurrency . ')'];
            return back()->withNotify($notify);
        }

        $charge = $gate->fixed_charge + ($request->amount * $gate->percent_charge / 100);
        $payable = $request->amount + $charge;
        $finalAmount = $payable;

        $data = new Deposit();
        $data->user_id = $user->id;
        $data->order_id = 0;
        $data->method_code = 0; // Taurixy / AlpPay (must match gateways.code)
        $data->method_currency = $selectedCurrency;
        $data->amount = $request->amount;
        $data->charge = $charge;
        $data->rate = 1.0;
        $data->final_amount = $finalAmount;
        $data->btc_amount = 0;
        $data->btc_wallet = "";
        $data->trx = getTrx();
        $data->status = Status::PAYMENT_INITIATE;
        $data->success_url = route('user.deposit.history');
        $data->failed_url = route('user.deposit.history');
        $data->save();

        session()->put('Track', $data->trx);

        // Skip local Payment Preview page and go directly to AlpPay hosted payment page
        return redirect()->route('user.deposit.alppay.create', ['deposit_trx' => $data->trx]);
    }

    public function depositConfirm()
    {
        $track = session()->get('Track');
        $deposit = Deposit::where('trx', $track)->orderBy('id', 'DESC')->with('gateway')->firstOrFail();

        $pageTitle = 'Payment Preview';
        $alppayEnabled = (bool) config('alppay.api_key');
        return view('Template::user.deposit.confirm', compact('deposit', 'pageTitle', 'alppayEnabled'));
    }

    public function depositManual()
    {
        $pageTitle = 'Deposit Manual';
        $deposits = auth()->user()->deposits()->where('status', Status::PAYMENT_PENDING)->orderBy('id', 'desc')->with('gateway')->paginate(getPaginate());
        return view('Template::user.deposit.manual', compact('pageTitle', 'deposits'));
    }

    // Покупка eSIM с помощью баланса
    public function purchaseWithBalance(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'quantity' => 'required|integer|min:1|max:10'
        ]);

        $user = auth()->user();
        $plan = Plan::findOrFail($request->plan_id);

        // Проверяем, что план активен
        if ($plan->status != Status::ENABLE) {
            $notify[] = ['error', 'This plan is not available'];
            return back()->withNotify($notify);
        }

        $quantity = $request->quantity;
        $totalAmount = planCustomerPrice($plan) * $quantity;

        // Проверяем баланс пользователя
        if ($user->balance < $totalAmount) {
            $notify[] = ['error', 'Insufficient balance. You need ' . showAmount($totalAmount)];
            return back()->withNotify($notify);
        }

        // Создаем заказ
        $order = new Order();
        $order->user_id = $user->id;
        $order->plan_id = $plan->id;
        $order->amount = $totalAmount;
        $order->quantity = $quantity;
        $order->status = Status::PAYMENT_SUCCESS; // Сразу успешный, так как оплачиваем балансом
        $order->trx = getTrx();
        $order->save();

        // Списываем средства с баланса
        $user->balance -= $totalAmount;
        $user->save();

        // Создаем транзакцию
        $transaction = new Transaction();
        $transaction->user_id = $user->id;
        $transaction->order_id = $order->id;
        $transaction->amount = $totalAmount;
        $transaction->post_balance = $user->balance;
        $transaction->charge = 0;
        $transaction->trx_type = '-';
        $transaction->details = 'Purchase of ' . $quantity . 'x ' . $plan->name;
        $transaction->trx = $order->trx;
        $transaction->remark = 'purchase';
        $transaction->save();

        // Здесь должна быть логика создания eSIM карт
        // Пока просто создаем запись в базе
        for ($i = 0; $i < $quantity; $i++) {
            \App\Models\Esim::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'order_id' => $order->id,
                'status' => Status::ENABLE,
                'activation_date' => now(),
                'expiry_date' => now()->addDays($plan->duration),
                'data_used' => 0,
                'data_limit' => $plan->volume,
                'trx' => getTrx(),
            ]);
        }

        $notify[] = ['success', 'eSIM purchased successfully with balance'];
        return redirect()->route('user.esim.active')->withNotify($notify);
    }
}