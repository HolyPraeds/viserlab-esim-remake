<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Esim;
use App\Models\Order;
use App\Services\OrderEmailService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends Controller {
    public function all($userId = null){
        $pageTitle = 'All Orders';
        $orders = $this->orderData(userId:$userId);
        return view('admin.order.index', compact('pageTitle', 'orders'));
    }

    public function pending($userId = null){
        $pageTitle = 'Pending Orders';
        $orders = $this->orderData('pending', $userId);
        return view('admin.order.index', compact('pageTitle', 'orders'));
    }

    public function completed($userId = null){
        $pageTitle = 'Completed Orders';
        $orders = $this->orderData('completed', $userId);
        return view('admin.order.index', compact('pageTitle', 'orders'));
    }

    protected function orderData($scope = null, $userId = null){
        if($scope){
            $query = Order::$scope();
        }else{
            $query = Order::query();
        }

        if($userId){
            $query->where('user_id', $userId);
        }

        return $query->with(['user', 'orderItem.plan', 'deposits'])
            ->searchable(['order_number', 'user:username'])
            ->dateFilter()
            ->orderBy('id', 'DESC')
            ->paginate(getPaginate());
    }

    public function esimApprove(Request $request) {
        $esims = explode(',', $request->esims);
        $request->merge(['esims' => $esims]);

        $request->validate([
            'esims'  => 'required|array|min:1',
            'status' => ['required', Rule::in([Status::ENABLE, Status::DISABLE])],
        ]);

        $esimModels = Esim::with(['user', 'orderItem.order.plan'])
            ->whereIn('id', $esims)
            ->get();

        foreach ($esimModels as $esim) {
            $esim->status = $request->status;
            $esim->save();

            notify($esim->user, 'ESIM_APPROVED', [
                'plan_name'       => $esim->orderItem->order->plan->name,
                'plan_capacity'   => $esim->orderItem->order->plan->capacity . $esim->orderItem->order->plan->capacity_unit,
                'phone_number'    => $esim->phone_number,
                'plan_price'      => showAmount($esim->orderItem->price),
                'plan_activation' => showDateTime($esim->created_at, 'd-M-Y, h:i A'),
                'expiry_date'     => showDateTime($esim->expiry_date, 'd-M-Y, h:i A'),
                'trx'             => $esim->orderItem->trx,
            ]);
        }

        $notify[] = ['success', 'Selected eSIMs have been updated'];
        return to_route('admin.esim.pending')->withNotify($notify);
    }

    /**
     * Resend purchase emails (PAYMENT_COMPLETED with QR, fallback ORDER_PLACED).
     * Sends to account email + checkout email. Optional extra address can be added.
     */
    public function resendOrderEmails(Request $request, string $orderNumber)
    {
        $request->validate([
            'email' => 'nullable|email',
        ]);

        $order = Order::with('user', 'orderItem.plan', 'deposits')->where('order_number', $orderNumber)->firstOrFail();
        $service = app(OrderEmailService::class);
        $extraEmail = $request->email;

        $result = $service->sendPaymentCompleted($order, $extraEmail);
        $fallbackUsed = false;
        if (!empty($result['error'])) {
            $result = $service->sendOrderPlaced($order, $extraEmail);
            $fallbackUsed = true;
        }

        $notify = [];
        if (!empty($result['error']) && empty($result['sent'])) {
            $notify[] = ['error', $result['error']];
            return back()->withNotify($notify);
        }

        if (!empty($result['sent'])) {
            $label = $fallbackUsed ? 'Order placed email' : 'Purchase email';
            $notify[] = ['success', $label . ' sent to: ' . implode(', ', $result['sent'])];
        }
        if (!empty($result['failed'])) {
            $notify[] = ['error', 'Failed for: ' . implode(', ', $result['failed'])];
        }

        return back()->withNotify($notify);
    }
}
