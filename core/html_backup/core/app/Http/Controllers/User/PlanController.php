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
}
