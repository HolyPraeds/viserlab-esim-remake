<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TaurixyService
{
    private $apiKey;
    private $shopId;
    private $baseUrl;
    private $signingKey;
    private $returnUrl;
    private $webhookUrl;

    public function __construct()
    {
        $this->apiKey = 'xZXxn6LXpasDbTcdUN6u1TsDKzHR27Ky';
        $this->shopId = '41';
        $this->baseUrl = 'https://engine-sandbox.taurixy.com';
        $this->signingKey = 'poy5QXDLl4tj';
        $this->returnUrl = rtrim(config('app.url'), '/') . '/user/order/payment/success';
        $this->webhookUrl = rtrim(config('app.url'), '/') . '/ipn/taurixy';
    }

    /**
     * Создать платеж
     */
    public function createPayment($data)
    {
        $paymentData = [
            'paymentType' => 'DEPOSIT',
            'amount' => $data['amount'],
            // Валюта по умолчанию EUR (для Европы). Можно переопределить через $data['currency']
            'currency' => $data['currency'] ?? 'EUR',
            'referenceId' => $data['reference_id'],
            'description' => $data['description'] ?? 'eSIM Purchase',
            'returnUrl' => $this->returnUrl,
            'webhookUrl' => $this->webhookUrl,
            'customer' => [
                'firstName' => $data['customer']['first_name'] ?? 'Guest',
                'lastName' => $data['customer']['last_name'] ?? 'User',
                'email' => $data['customer']['email'] ?? 'guest@example.com',
                'phone' => $this->formatPhone($data['customer']['phone'] ?? '123 4567890'),
            ]
        ];

        // Принудительно включаем BASIC_CARD для HPP (Browser redirect)
        $paymentData['paymentMethod'] = 'BASIC_CARD';

        // Добавляем адрес если указан (ISO2 country code обязателен)
        if (isset($data['billing_address'])) {
            $paymentData['billingAddress'] = $data['billing_address'];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . '/api/v1/payments', $paymentData);

            Log::info('Taurixy payment request', [
                'data' => $paymentData,
                'response' => $response->json()
            ]);

            if ($response->successful()) {
                return $response->json();
            } else {
                Log::error('Taurixy payment failed', [
                    'status' => $response->status(),
                    'response' => $response->json()
                ]);
                return ['error' => 'Payment creation failed', 'details' => $response->json()];
            }
        } catch (\Exception $e) {
            Log::error('Taurixy payment exception', ['error' => $e->getMessage()]);
            return ['error' => 'Payment service error', 'details' => $e->getMessage()];
        }
    }

    /**
     * Получить статус платежа
     */
    public function getPaymentStatus($paymentId)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json'
            ])->get($this->baseUrl . '/api/v1/payments/' . $paymentId);

            if ($response->successful()) {
                return $response->json();
            } else {
                Log::error('Taurixy payment status failed', [
                    'payment_id' => $paymentId,
                    'status' => $response->status(),
                    'response' => $response->json()
                ]);
                return ['error' => 'Failed to get payment status'];
            }
        } catch (\Exception $e) {
            Log::error('Taurixy payment status exception', ['error' => $e->getMessage()]);
            return ['error' => 'Payment service error'];
        }
    }

    /**
     * Проверить подпись webhook
     */
    public function verifyWebhookSignature($payload, $signature)
    {
        $expectedSignature = hash_hmac('sha256', $payload, $this->signingKey);
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Обработать webhook
     */
    public function processWebhook($payload, $signature)
    {
        if (!$this->verifyWebhookSignature($payload, $signature)) {
            Log::error('Taurixy webhook signature verification failed');
            return false;
        }

        $data = json_decode($payload, true);
        
        if (!$data || !isset($data['result'])) {
            Log::error('Taurixy webhook invalid payload');
            return false;
        }

        $payment = $data['result'];
        
        Log::info('Taurixy webhook received', [
            'payment_id' => $payment['id'] ?? null,
            'state' => $payment['state'] ?? null,
            'reference_id' => $payment['referenceId'] ?? null
        ]);

        // Здесь будет логика обновления платежа в базе данных
        return $this->updatePaymentFromWebhook($payment);
    }

    /**
     * Обновить платеж из webhook
     */
    private function updatePaymentFromWebhook($paymentData)
    {
        try {
            $payment = \App\Models\Payment::where('taurixy_payment_id', $paymentData['id'])->first();
            
            if (!$payment) {
                Log::error('Taurixy payment not found in database', ['payment_id' => $paymentData['id']]);
                return false;
            }

            $payment->update([
                'state' => $paymentData['state'],
                'webhook_data' => $paymentData,
                'error_code' => $paymentData['errorCode'] ?? null,
                'error_message' => $paymentData['errorMessage'] ?? null,
            ]);

            // Если платеж завершен успешно, активируем eSIM
            if ($paymentData['state'] === 'COMPLETED') {
                $this->activateESIM($payment);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Taurixy webhook processing error', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Активировать eSIM после успешной оплаты
     */
    private function activateESIM($payment)
    {
        try {
            // Здесь будет логика активации eSIM
            Log::info('Activating eSIM for payment', ['payment_id' => $payment->id]);
            
            // Обновляем статус заказа
            $payment->update(['description' => 'eSIM activated successfully']);
            
        } catch (\Exception $e) {
            Log::error('eSIM activation error', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Форматировать номер телефона для Taurixy API
     */
    private function formatPhone($phone)
    {
        if (!$phone) {
            return '123 4567890';
        }
        
        // Убираем все нецифровые символы
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Если номер длинный, добавляем пробел после первых 3 цифр
        if (strlen($phone) >= 3) {
            return substr($phone, 0, 3) . ' ' . substr($phone, 3);
        }
        
        return $phone;
    }
    
}
