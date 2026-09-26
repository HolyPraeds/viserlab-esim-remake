<?php

namespace App\Http\Controllers\IPN;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Esim;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EsimAccessController extends Controller
{
    public function ipn(Request $request)
    {
        if ($request->isMethod('GET')) {
            return response('eSIM Access webhook endpoint — use POST for notifications.', 200);
        }

        $payload = $request->getContent();
        Log::info('EsimAccess IPN received', [
            'headers' => $request->headers->all(),
            'payload' => $payload,
        ]);

        $data = json_decode($payload, true);
        $notifyType = $data['notifyType'] ?? null;
        $content = $data['content'] ?? [];

        if ($notifyType === 'ORDER_STATUS' && ($content['orderStatus'] ?? null) === 'GOT_RESOURCE') {
            $orderNo = $content['orderNo'] ?? null;
            $transactionId = $content['transactionId'] ?? '';
            if ($orderNo && $transactionId) {
                // Our transactionId format: ORD-{order_number}-{uniqid}
                $parts = explode('-', $transactionId);
                $orderNumber = $parts[1] ?? null;
                if ($orderNumber) {
                    $order = Order::with('orderItem.plan')->where('order_number', $orderNumber)->first();
                    if (!$order) {
                        Log::warning('EsimAccess IPN: order not found', ['order_number' => $orderNumber]);

                        return response('OK', 200);
                    }

                    $esimService = app(\App\Services\EsimAccessService::class);
                    $result = $esimService->fetchProfilesByOrderNo($orderNo);
                    if (isset($result['error']) || empty($result['purchase']['esim'] ?? null)) {
                        // Profiles sometimes appear a second later after GOT_RESOURCE
                        usleep(2_000_000);
                        $result = $esimService->fetchProfilesByOrderNo($orderNo);
                    }

                    if (isset($result['error']) || empty($result['purchase']['esim'] ?? null)) {
                        Log::warning('EsimAccess IPN: could not fetch profiles', [
                            'order_number' => $orderNumber,
                            'orderNo' => $orderNo,
                            'error' => $result['error'] ?? 'no esim',
                        ]);

                        return response('OK', 200);
                    }

                    $orderItem = $order->orderItem;
                    $hasEsim = $orderItem && Esim::where('order_item_id', $orderItem->id)->exists();

                    // Gateway card payment must NOT use paidFromWallet=true (that deducts credits and is wrong here).
                    if ((int) $order->status === Status::ORDER_COMPLETED && $hasEsim) {
                        return response('OK', 200);
                    }

                    if ((int) $order->status === Status::ORDER_COMPLETED && !$hasEsim) {
                        dataPlans()->createEsimRecordsOnly($order, $result);
                        Log::info('EsimAccess IPN: created missing eSIM rows for completed order', [
                            'order_number' => $orderNumber,
                            'orderNo' => $orderNo,
                        ]);

                        return response('OK', 200);
                    }

                    if ((int) $order->status === Status::ORDER_PENDING) {
                        dataPlans()->completeOrderWithEsimData($order, $result, false);
                        Log::info('EsimAccess IPN: completed pending order', [
                            'order_number' => $orderNumber,
                            'orderNo' => $orderNo,
                        ]);
                    }
                }
            }
        }

        return response('OK', 200);
    }
}
