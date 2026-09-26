<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller {
    private function prohibitedCountryCodes(): array
    {
        return ['RU', 'BY', 'IR', 'SY', 'KP', 'MM', 'VE', 'AF', 'LY', 'SD', 'YE'];
    }

    private function isPlanInProhibitedJurisdiction(Plan $plan): bool
    {
        return $plan->countries()->whereIn('code', $this->prohibitedCountryCodes())->exists();
    }

    public function purchase(Request $request) {
        if (!auth()->check()) {
            $notify[] = ['error', 'Please login or register to continue purchase'];
            return redirect()->route('user.login')->withNotify($notify);
        }

        // plan_id может отсутствовать при гостевом чекауте с фронта
        $planId = $request->input('plan_id');

        if ($planId) {
            $plan = Plan::active()->withPositivePrice()->findOrFail($planId);
        } else {
            $plan = Plan::active()->withPositivePrice()->firstOrFail();
        }

        if ($this->isPlanInProhibitedJurisdiction($plan)) {
            $notify[] = ['error', 'This destination is not available in your jurisdiction.'];
            return redirect()->route('destination')->withNotify($notify);
        }

        $customerPriceEur = planCustomerPrice($plan);
        if ($customerPriceEur < 0.01) {
            $notify[] = ['error', 'This plan is currently unavailable.'];
            return redirect()->route('destination')->withNotify($notify);
        }

        $user = auth()->user();
        
        // Get selected currency from form (EUR, GBP, or USD)
        $siteCurrency = $request->input('site_currency', 'EUR');
        \Log::info('Plan purchase - received currency', [
            'site_currency' => $siteCurrency,
            'all_input' => $request->all(),
        ]);
        
        // Fixed exchange rates: 1 EUR = 0.87 GBP, 1 EUR = 1.18 USD
        $EXCHANGE_RATES = [
            'GBP' => 0.87,
            'USD' => 1.18
        ];
        
        // Calculate price based on selected currency
        $orderAmount = $customerPriceEur;
        if ($siteCurrency !== 'EUR' && isset($EXCHANGE_RATES[$siteCurrency])) {
            // Convert EUR to target currency
            $orderAmount = $customerPriceEur * $EXCHANGE_RATES[$siteCurrency];
            \Log::info('Currency conversion applied', [
                'base_price_eur' => $customerPriceEur,
                'converted_price' => $orderAmount,
                'target_currency' => $siteCurrency,
                'rate' => $EXCHANGE_RATES[$siteCurrency],
            ]);
        }

        // Пропускаем внешний чек доступности
        $order = new Order();
        $order->user_id = $user->id;
        $order->order_number = getTrx(10);
        $order->total_amount = $orderAmount;
        $order->payment_currency = $siteCurrency; // Save selected currency
        $order->status = \App\Constants\Status::ORDER_PENDING; // Set to PENDING so it shows in dashboard
        $order->save();

        $orderItem = new OrderItem();
        $orderItem->plan_id = $plan->id;
        $orderItem->order_id = $order->id;
        $orderItem->price  = $orderAmount; // Use converted price
        $orderItem->save();

        \Log::info('Order created from purchase', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'user_id' => $order->user_id,
            'plan_id' => $plan->id,
            'base_price' => $customerPriceEur,
            'total_amount' => $order->total_amount,
            'payment_currency' => $order->payment_currency,
        ]);

        // Store order number in session (used as fallback on paymentSuccess in local env)
        session(['last_order_number' => $order->order_number]);

        return to_route('user.order.payment', $order->order_number);
    }

    public function buyFromWallet(Request $request) {
        if (!auth()->check()) {
            $notify[] = ['error', 'Please login or register to continue purchase'];
            return redirect()->route('user.login')->withNotify($notify);
        }

        \Log::info('=== buyFromWallet method called ===', [
            'plan_id' => $request->input('plan_id'),
            'user_id' => auth()->id(),
            'user_balance' => auth()->user()->balance,
            'all_input' => $request->all(),
            'request_method' => $request->method(),
            'request_url' => $request->fullUrl()
        ]);
        
        $planId = $request->input('plan_id');
        $plan = Plan::active()->withPositivePrice()->findOrFail($planId);
        if ($this->isPlanInProhibitedJurisdiction($plan)) {
            $notify[] = ['error', 'This destination is not available in your jurisdiction.'];
            return redirect()->route('destination')->withNotify($notify);
        }
        $user = auth()->user();
        $customerPriceEur = planCustomerPrice($plan);
        if ($customerPriceEur < 0.01) {
            $notify[] = ['error', 'This plan is currently unavailable.'];
            return redirect()->route('destination')->withNotify($notify);
        }

        // Check if user has enough balance
        if ($user->balance < $customerPriceEur) {
            $notify[] = ['error', 'Insufficient balance in your wallet'];
            return back()->withNotify($notify);
        }

        // Create order
        $order = new Order();
        $order->user_id = $user->id;
        $order->order_number = getTrx(10);
        $order->total_amount = $customerPriceEur;
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
        $orderItem->price = $customerPriceEur;
        $orderItem->save();

        // Deduct amount from user balance
        $user->balance -= $customerPriceEur;
        $user->save();

        // Create transaction record
        $transaction = new \App\Models\Transaction();
        $transaction->user_id = $user->id;
        $transaction->amount = $customerPriceEur;
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
