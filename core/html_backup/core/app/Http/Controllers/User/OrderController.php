<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController;
use App\Models\GatewayCurrency;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Services\TaurixyService;

class OrderController extends Controller {
    public function pending() {
        $pageTitle = 'Pending Orders';
        $orders = Order::pending()->where('user_id', auth()->id())->orderBy('id', 'DESC')->paginate(getPaginate());
        return view('Template::user.order.index', compact('pageTitle', 'orders'));
    }

    public function completed() {
        $pageTitle = 'Completed Orders';
        $orders = Order::completed()->where('user_id', auth()->id())->orderBy('id', 'DESC')->paginate(getPaginate());
        return view('Template::user.order.index', compact('pageTitle', 'orders'));
    }

    public function payment($id) {

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

        $gatewayCurrency = GatewayCurrency::where(function ($query) use ($order) {
            $query->where('min_amount', '<=', $order->total_amount)->where('max_amount', '>=', $order->total_amount);
        })->whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->with('method')->orderBy('name')->get();

        $pageTitle = 'Payment Methods';
        return view('Template::user.order.payment', compact('pageTitle', 'gatewayCurrency',  'order'));
    }



    public function paymentInitiate(Request $request) {
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

        if ($order->total_amount > $user->balance) {
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

    // Direct Taurixy payment without gateway records
    public function taurixyDirect(Request $request)
    {
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

        // map country name to ISO2 (very small map + fallback)
        $countryMap = [
            'Latvia' => 'LV',
            'United States' => 'US',
            'USA' => 'US',
            'United Kingdom' => 'GB',
            'UK' => 'GB',
            'Germany' => 'DE',
            'France' => 'FR',
        ];
        $countryInput = trim((string) $request->input('country'));
        $countryCode = $countryMap[$countryInput] ?? (strlen($countryInput) === 2 ? strtoupper($countryInput) : 'LV');

        $billingAddress = [
            'countryCode' => $countryCode,
            'city' => $request->input('city'),
            'addressLine1' => $request->input('address'),
            'postalCode' => $request->input('zip'),
        ];

        $service = new TaurixyService();
        $response = $service->createPayment([
            'amount' => (float) $order->total_amount,
            'currency' => gs('cur_text') ?? 'USD',
            'reference_id' => $order->order_number,
            'description' => 'eSIM Order #' . $order->order_number,
            'payment_method' => $request->input('payment_method', 'BASIC_CARD'),
            'customer' => [
                'first_name' => $request->input('first_name', auth()->user()->firstname ?? 'Guest'),
                'last_name' => $request->input('last_name', auth()->user()->lastname ?? 'User'),
                'email' => $request->input('email', auth()->user()->email ?? 'guest@example.com'),
                'phone' => $request->input('phone', auth()->user()->mobile ?? '1234567890'),
            ],
            'billing_address' => $billingAddress,
        ]);

        if (!empty($response['result']['redirectUrl'])) {
            return redirect()->away($response['result']['redirectUrl']);
        }

        $notify[] = ['error', $response['error'] ?? 'Failed to create Taurixy payment'];
        return back()->withNotify($notify);
    }

    public function paymentSuccess(Request $request)
    {
        $pageTitle = 'Payment Successful';
        
        // Dev fallback: Check if we have order info in session or request
        // This handles cases where webhook can't reach localhost
        $orderNumber = session('last_order_number') ?? $request->get('order_number');
        
        if ($orderNumber && config('app.env') === 'local') {
            $order = Order::where('order_number', $orderNumber)->where('status', \App\Constants\Status::ORDER_PENDING)->first();
            
            if ($order) {
                \Log::info('Dev fallback: Auto-confirming order from payment success', [
                    'order_number' => $orderNumber,
                    'order_id' => $order->id
                ]);
                
                // Auto-confirm the order for local development
                $order->status = \App\Constants\Status::ORDER_COMPLETED;
                $order->save();
                
                // Process the purchase
                $result = dataPlans()->confirmPurchase($order, false);
                
                if ($result['status']) {
                    $notify[] = ['success', 'Payment completed successfully! Your eSIM has been activated.'];
                } else {
                    $notify[] = ['warning', 'Payment completed but eSIM activation failed: ' . $result['message']];
                }
            } else {
                $notify[] = ['success', 'Payment completed successfully! Your eSIM will be available in your account shortly.'];
            }
        } else {
            $notify[] = ['success', 'Payment completed successfully! Your eSIM will be available in your account shortly.'];
        }
        
        return view('templates.basic.user.order.success', compact('pageTitle'))->withNotify($notify);
    }
}
