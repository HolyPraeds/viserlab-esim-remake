<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Log;

class EsimAccessService
{
    private string $baseUrl;
    private ?string $accessCode;
    private ?string $secretKey;

    public function __construct()
    {
        $this->baseUrl   = rtrim(env('ESIMACCESS_BASE_URL', 'https://api.esimaccess.com/api/v1/open'), '/');
        $this->accessCode = config('services.esim.access_code');
        $this->secretKey  = config('services.esim.secret_key');
    }

    private function buildHeaders(): array
    {
        $headers = [
            'Content-Type: application/json',
        ];
        if ($this->accessCode) {
            $headers[] = 'RT-AccessCode: ' . $this->accessCode;
        }
        return $headers;
    }

    public function purchase(Order $order): array
    {
        try {
            $orderItem = $order->orderItem;

            $payload = [
                // NOTE: Map your plan to provider SKU/planId as needed
                'slug' => $orderItem->plan->slug,
            ];

            $response = \App\Lib\CurlRequest::curlPostContent(
                $this->baseUrl . '/purchases',
                json_encode($payload),
                $this->buildHeaders()
            );

            $decoded = json_decode($response, true);

            if (!is_array($decoded)) {
                return ['error' => 'Invalid response from eSIM provider'];
            }

            return $decoded;
        } catch (\Throwable $e) {
            Log::error('EsimAccess purchase error', ['error' => $e->getMessage()]);
            return ['error' => $e->getMessage()];
        }
    }
}




