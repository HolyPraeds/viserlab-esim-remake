<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController;
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
        $pageTitle = 'Deposit History';
        $deposits = auth()->user()->deposits()->searchable(['trx'])->with(['gateway'])->orderBy('id', 'desc')->paginate(getPaginate());
        return view('Template::user.deposit.index', compact('pageTitle', 'deposits'));
    }

    public function deposit()
    {
        // Только Taurixy для пополнения
        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE)->where('id', 0);
        })->where('currency', 'EUR')->with('method')->first();

        if (!$gatewayCurrency) {
            $notify[] = ['error', 'Taurixy payment method not available'];
            return back()->withNotify($notify);
        }

        $pageTitle = 'Add Balance';
        return view('Template::user.payment.deposit', compact('gatewayCurrency', 'pageTitle'));
    }

    public function depositInsert(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|gt:0'
        ]);

        $user = auth()->user();
        
        // Только Taurixy gateway
        $gate = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE)->where('id', 0);
        })->where('currency', 'EUR')->first();

        if (!$gate) {
            $notify[] = ['error', 'Taurixy payment method not available'];
            return back()->withNotify($notify);
        }

        if ($gate->min_amount > $request->amount || $gate->max_amount < $request->amount) {
            $notify[] = ['error', 'Please follow deposit limit'];
            return back()->withNotify($notify);
        }

        $charge = $gate->fixed_charge + ($request->amount * $gate->percent_charge / 100);
        $payable = $request->amount + $charge;
        $finalAmount = $payable; // Для EUR не конвертируем

        $data = new Deposit();
        $data->user_id = $user->id;
        $data->method_code = '0'; // Только Taurixy
        $data->method_currency = 'EUR';
        $data->amount = $request->amount;
        $data->charge = $charge;
        $data->rate = 1.0; // Для EUR
        $data->final_amount = $finalAmount;
        $data->btc_amount = 0;
        $data->btc_wallet = "";
        $data->trx = getTrx();
        $data->success_url = route('user.deposit.history');
        $data->failed_url = route('user.deposit.history');
        $data->save();

        session()->put('Track', $data->trx);

        $notify[] = ['success', 'Deposit request created successfully'];
        return redirect()->route('user.deposit.confirm')->withNotify($notify);
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
        $totalAmount = $plan->retail_price * $quantity;

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

    // Обновление баланса после успешного депозита
    public static function userDataUpdate($deposit, $isManual = false)
    {
        if ($deposit->status == Status::PAYMENT_SUCCESS && $deposit->order_id == 0) {
            $user = User::find($deposit->user_id);
            $user->balance += $deposit->amount;
            $user->save();

            $transaction = new Transaction();
            $transaction->user_id = $deposit->user_id;
            $transaction->amount = $deposit->amount;
            $transaction->post_balance = $user->balance;
            $transaction->charge = $deposit->charge;
            $transaction->trx_type = '+';
            $transaction->details = 'Deposit by Card';
            $transaction->trx = $deposit->trx;
            $transaction->remark = 'deposit';
            $transaction->save();

            if ($isManual) {
                $notify[] = ['success', 'Deposit approved successfully'];
                return back()->withNotify($notify);
            }
        }
    }
}