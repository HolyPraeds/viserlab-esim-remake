<?php

namespace App\Http\Controllers\Gateway\Taurixy;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController;
use App\Models\Deposit;
use App\Models\Gateway;
use App\Services\TaurixyService;
use Illuminate\Http\Request;

class ProcessController extends Controller
{
    public static function process($deposit)
    {
        $taurixyService = new TaurixyService();
        
        // Получаем данные пользователя
        $user = $deposit->user;
        
        // Подготавливаем данные для платежа
        $paymentData = [
            'amount' => $deposit->final_amount,
            'currency' => $deposit->method_currency ?? 'USD',
            'reference_id' => $deposit->trx,
            'description' => 'eSIM Purchase - Order #' . $deposit->trx,
            'payment_method' => 'BASIC_CARD',
            'customer' => [
                'first_name' => $user->firstname ?? 'Guest',
                'last_name' => $user->lastname ?? 'User',
                'email' => $user->email,
                'phone' => $user->mobile ?? '1234567890',
            ]
        ];
        
        // Создаем платеж через Taurixy
        $response = $taurixyService->createPayment($paymentData);
        
        if (isset($response['error'])) {
            $send['error'] = true;
            $send['message'] = $response['error'];
            return json_encode($send);
        }
        
        if (!isset($response['result'])) {
            $send['error'] = true;
            $send['message'] = 'Failed to create payment';
            return json_encode($send);
        }
        
        // Сохраняем данные платежа в депозит
        $deposit->btc_wallet = $response['result']['id'] ?? null;
        $deposit->update();
        
        // Возвращаем данные для редиректа
        $send['redirect'] = true;
        $send['redirect_url'] = $response['result']['redirectUrl'] ?? null;
        
        return json_encode($send);
    }
    
    public function ipn(Request $request)
    {
        $taurixyService = new TaurixyService();
        
        // Получаем данные из webhook
        $payload = $request->getContent();
        $signature = $request->header('X-Taurixy-Signature');
        
        // Проверяем подпись
        if (!$taurixyService->verifyWebhookSignature($payload, $signature)) {
            return response('Invalid signature', 400);
        }
        
        $data = json_decode($payload, true);
        
        if (!$data || !isset($data['result'])) {
            return response('Invalid payload', 400);
        }
        
        $paymentId = $data['result']['id'] ?? null;
        $state = $data['result']['state'] ?? null;
        $referenceId = $data['result']['referenceId'] ?? null;
        
        if (!$paymentId || !$referenceId) {
            return response('Missing required fields', 400);
        }
        
        // Пытаемся закрыть заказ по order_number, иначе интерпретируем reference как trx депозита
        $order = \App\Models\Order::where('order_number', $referenceId)->first();
        if ($order) {
            if ($state === 'COMPLETED') {
                $order->status = 1; // Completed
                $order->save();
                $orderItem = $order->orderItem;
                if ($orderItem) {
                    $result = dataPlans()->confirmPurchase($order, false);
                    return response($result['status'] ? 'OK' : ('Purchase confirmation failed: ' . $result['message']), $result['status'] ? 200 : 500);
                }
            } elseif ($state === 'FAILED' || $state === 'CANCELLED') {
                $order->status = 2; // Failed
                $order->save();
            }
            return response('OK', 200);
        }

        // Если это не заказ, обрабатываем как пополнение кошелька по trx
        $deposit = Deposit::where('trx', $referenceId)->first();
        if ($deposit) {
            if ($state === 'COMPLETED') {
                PaymentController::userDataUpdate($deposit);
            }
            return response('OK', 200);
        }

        return response('Not found', 404);
    }
}
