<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller {

    public function purchase(Request $request) {
        // plan_id может отсутствовать при гостевом чекауте с фронта
        $planId = $request->input('plan_id');

        if ($planId) {
            $plan = Plan::active()->findOrFail($planId);
        } else {
            $plan = Plan::active()->firstOrFail();
        }

        $user = auth()->user();

        // Пропускаем внешний чек доступности
        $order = new Order();
        $order->user_id = $user?->id ?? 0;
        $order->order_number = getTrx(10);
        $order->total_amount = $plan->price;
        $order->status = \App\Constants\Status::ORDER_PENDING; // Set to PENDING so it shows in dashboard
        $order->save();

        $orderItem = new OrderItem();
        $orderItem->plan_id = $plan->id;
        $orderItem->order_id = $order->id;
        $orderItem->price  = $plan->price;
        $orderItem->save();

        \Log::info('Order created from purchase', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'user_id' => $order->user_id,
            'plan_id' => $plan->id,
            'total_amount' => $order->total_amount,
        ]);

        // Store order number in session for dev fallback
        session(['last_order_number' => $order->order_number]);

        return to_route('user.order.payment', $order->order_number);
    }

    public function buyFromWallet(Request $request) {
        \Log::info('=== buyFromWallet method called ===', [
            'plan_id' => $request->input('plan_id'),
            'user_id' => auth()->id(),
            'user_balance' => auth()->user()->balance,
            'all_input' => $request->all(),
            'request_method' => $request->method(),
            'request_url' => $request->fullUrl()
        ]);
        
        $planId = $request->input('plan_id');
        $plan = Plan::active()->findOrFail($planId);
        $user = auth()->user();

        // Check if user has enough balance
        if ($user->balance < $plan->price) {
            $notify[] = ['error', 'Insufficient balance in your wallet'];
            return back()->withNotify($notify);
        }

        // Create order
        $order = new Order();
        $order->user_id = $user->id;
        $order->order_number = getTrx(10);
        $order->total_amount = $plan->price;
        $order->status = \App\Constants\Status::ORDER_COMPLETED; // Set to COMPLETED since paid from wallet
        $order->save();

        \Log::info('Order created from wallet purchase', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'user_id' => $order->user_id,
            'plan_id' => $plan->id,
            'total_amount' => $order->total_amount,
            'status' => $order->status,
        ]);

        $orderItem = new OrderItem();
        $orderItem->plan_id = $plan->id;
        $orderItem->order_id = $order->id;
        $orderItem->price = $plan->price;
        $orderItem->save();

        // Deduct amount from user balance
        $user->balance -= $plan->price;
        $user->save();

        // Create transaction record
        $transaction = new \App\Models\Transaction();
        $transaction->user_id = $user->id;
        $transaction->amount = $plan->price;
        $transaction->post_balance = $user->balance;
        $transaction->charge = 0;
        $transaction->trx_type = '-';
        $transaction->details = 'Purchase eSIM plan';
        $transaction->trx = getTrx();
        $transaction->remark = 'purchase';
        $transaction->save();

        // Create eSIM records
        for ($i = 0; $i < 1; $i++) { // Assuming 1 eSIM per plan
            \App\Models\Esim::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'order_item_id' => $orderItem->id,
                'serial_number' => getTrx(),
                'phone_number' => null,
                'qr_code' => null,
                'expiry_date' => now()->addDays($plan->validity_days ?? 30),
            ]);
        }

        $notify[] = ['success', 'eSIM purchased successfully from wallet'];
        return redirect()->route('user.esim.active')->withNotify($notify);
    }
}
