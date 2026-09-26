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

    /**
     * Check eSIM Access balance (for testing connection).
     * POST https://api.esimaccess.com/api/v1/open/balance/query
     */
    public function getBalance(): array
    {
        try {
            $response = \App\Lib\CurlRequest::curlPostContent(
                $this->baseUrl . '/balance/query',
                '{}',
                $this->buildHeaders()
            );
            $decoded = json_decode($response, true);
            return is_array($decoded) ? $decoded : ['error' => 'Invalid response'];
        } catch (\Throwable $e) {
            Log::error('EsimAccess balance check error', ['error' => $e->getMessage()]);
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Usage Check: data usage of up to 10 eSIMs by esimTranNo.
     * POST https://api.esimaccess.com/api/v1/open/esim/usage/query
     * Body: {"esimTranNoList": ["25030303480009"]}
     */
    public function getUsage(array $esimTranNoList): array
    {
        try {
            $payload = ['esimTranNoList' => array_slice($esimTranNoList, 0, 10)];
            $response = \App\Lib\CurlRequest::curlPostContent(
                $this->baseUrl . '/esim/usage/query',
                json_encode($payload),
                $this->buildHeaders()
            );
            $decoded = json_decode($response, true);
            return is_array($decoded) ? $decoded : ['error' => 'Invalid response'];
        } catch (\Throwable $e) {
            Log::error('EsimAccess usage check error', ['error' => $e->getMessage()]);
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Order profiles (single or batch).
     * POST https://api.esimaccess.com/api/v1/open/esim/order
     * Body: transactionId, packageCode (slug), count; optional price×count=amount, periodNum.
     * Success returns orderNo; profiles are allocated async — query via /esim/query.
     */
    public function purchase(Order $order): array
    {
        try {
            $orderItem = $order->orderItem;
            $plan = $orderItem->plan;
            $slug = $plan->slug ?? '';
            $transactionId = 'ORD-' . $order->order_number . '-' . uniqid();

            $count = (int) ($orderItem->quantity ?? 1);
            if ($count < 1) {
                $count = 1;
            }

            // API expects packageInfoList. Price/amount are optional; API uses USD, we sell with markup (often EUR).
            // Do not send our order total — let provider charge their current USD price from our balance.
            $packageInfo = ['packageCode' => $slug, 'count' => $count];
            if (env('ESIMACCESS_SEND_PRICE', false)) {
                $priceAmount = (int) round((float) $order->total_amount * 10000);
                $packageInfo['price'] = (int) round($priceAmount / $count);
            }
            $payload = [
                'transactionId'   => $transactionId,
                'packageInfoList' => [$packageInfo],
            ];
            if (env('ESIMACCESS_SEND_PRICE', false)) {
                $payload['amount'] = (int) round((float) $order->total_amount * 10000);
            }

            $orderPath = env('ESIMACCESS_ORDER_PATH') ? trim(env('ESIMACCESS_ORDER_PATH'), '/') : 'esim/order';
            $pathsToTry = [$orderPath];
            $decoded = null;
            $lastResponse = '';
            $lastUrl = '';
            $triedPaths = [];

            foreach ($pathsToTry as $orderPath) {
                $url = $this->baseUrl . '/' . $orderPath;
                $lastUrl = $url;
                $triedPaths[] = $orderPath;
                $lastResponse = \App\Lib\CurlRequest::curlPostContent(
                    $url,
                    json_encode($payload),
                    $this->buildHeaders()
                );
                $decoded = json_decode($lastResponse, true);
                $is404 = is_array($decoded) && isset($decoded['errorCode']) && $decoded['errorCode'] === '404';
                if ($is404) {
                    $decoded = null;
                    continue;
                }
                if (is_array($decoded) && (isset($decoded['success']) ? $decoded['success'] : true)) {
                    break;
                }
                break;
            }

            if (!is_array($decoded)) {
                $decoded = json_decode($lastResponse, true);
            }

            Log::info('EsimAccess order response', [
                'tried_paths' => $triedPaths,
                'last_url'    => $lastUrl,
                'order_id'    => $order->id,
                'plan_slug'   => $slug,
                'raw_length'  => strlen($lastResponse),
                'decoded'     => $decoded,
            ]);

            if (!is_array($decoded)) {
                return ['error' => 'Invalid response from eSIM provider', '_raw' => substr($lastResponse, 0, 500)];
            }

            if (isset($decoded['success']) && $decoded['success'] === false) {
                $msg = $decoded['errorMsg'] ?? $decoded['errorCode'] ?? 'Order failed';
                return ['error' => $msg, 'errorCode' => $decoded['errorCode'] ?? null];
            }

            // Already have purchase.esim (single-step response)?
            $purchase = $decoded['purchase'] ?? $decoded['obj']['purchase'] ?? null;
            $obj = $decoded['obj'] ?? [];
            if (!empty($purchase['esim'])) {
                return $decoded;
            }
            // Or esimList in obj?
            $esimList = $obj['esimList'] ?? $obj['esim'] ?? [];
            $first = is_array($esimList) && isset($esimList[0]) ? $esimList[0] : $esimList;
            if (!empty($first) && (isset($first['serialNumber']) || isset($first['serial']) || isset($first['qrCodeString']))) {
                $esim = [
                    'serial'       => $first['serialNumber'] ?? $first['serial'] ?? '',
                    'phone'        => $first['phoneNumber'] ?? $first['phone'] ?? '',
                    'qrCodeString' => $first['qrCodeString'] ?? $first['qrCode'] ?? $first['lpaString'] ?? '',
                    'expiryDate'   => $first['expiryDate'] ?? $first['expiry'] ?? null,
                ];
                return [
                    'success'  => true,
                    'obj'      => $decoded['obj'] ?? [],
                    'purchase' => [
                        'purchaseId' => $obj['orderNo'] ?? $obj['orderNumber'] ?? '',
                        'paid'       => $obj['paid'] ?? $obj['amount'] ?? 0,
                        'esim'       => $esim,
                    ],
                ];
            }

            // Response has order number only -> Query Allocated Profiles to get eSIM
            $orderNo = $obj['orderNo'] ?? $decoded['orderNo'] ?? $obj['orderNumber'] ?? null;
            if ($orderNo) {
                $esimPayload = $this->queryAllocatedProfiles($orderNo);
                if (isset($esimPayload['error'])) {
                    return $esimPayload;
                }
                return array_merge($decoded, $esimPayload);
            }

            return $decoded;
        } catch (\Throwable $e) {
            Log::error('EsimAccess purchase error', ['error' => $e->getMessage(), 'order_id' => $order->id ?? null]);
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Public: fetch eSIM profiles by eSIM Access orderNo (e.g. from webhook ORDER_STATUS GOT_RESOURCE).
     * Returns same structure as purchase(): ['purchase' => ['purchaseId', 'paid', 'esim' => [...]]] or ['error' => ...].
     */
    public function fetchProfilesByOrderNo(string $orderNo): array
    {
        return $this->queryAllocatedProfiles($orderNo);
    }

    /**
     * Query Allocated Profiles: get eSIM payload by order number.
     */
    private function queryAllocatedProfiles(string $orderNo): array
    {
        $url = $this->baseUrl . '/esim/query';
        // API returns "pager:must not be null" without pagination params
        $payload = [
            'orderNo' => $orderNo,
            'pager'   => ['pageNum' => 1, 'pageSize' => 10],
        ];
        $response = \App\Lib\CurlRequest::curlPostContent($url, json_encode($payload), $this->buildHeaders());
        $decoded = json_decode($response, true);

        if (!is_array($decoded) || empty($decoded['success'])) {
            return ['error' => $decoded['errorMsg'] ?? 'Failed to get eSIM details'];
        }

        $obj = $decoded['obj'] ?? [];
        $rawList = $obj['esimList'] ?? $obj['esim'] ?? [];
        if (!is_array($rawList)) {
            $rawList = [$rawList];
        }
        if (empty($rawList)) {
            return ['error' => 'No eSIM in response'];
        }
        $paid = $obj['paid'] ?? $obj['amount'] ?? 0;
        $purchaseId = $obj['purchaseId'] ?? $orderNo;

        $esimList = [];
        foreach ($rawList as $first) {
            if (empty($first) || !is_array($first)) {
                continue;
            }
            $qrUrl = $first['qrCodeUrl'] ?? $first['qrCodeImageUrl'] ?? $first['qrImageUrl'] ?? $first['activationPageUrl'] ?? $first['qrPageUrl'] ?? '';
            $qrData = $first['qrCodeString'] ?? $first['qrCode'] ?? $first['lpaString'] ?? '';
            $esimList[] = [
                'serial'       => $first['serialNumber'] ?? $first['serial'] ?? $first['iccid'] ?? '',
                'phone'        => $first['phoneNumber'] ?? $first['phone'] ?? '',
                'qrCodeString' => $qrUrl ?: $qrData,
                'expiryDate'   => $first['expiryDate'] ?? $first['expiry'] ?? null,
            ];
        }
        if (empty($esimList)) {
            return ['error' => 'No eSIM in response'];
        }

        return [
            'purchase' => [
                'purchaseId' => $purchaseId,
                'paid'       => $paid,
                'esim'       => $esimList[0],
                'esimList'   => $esimList,
            ],
        ];
    }
}




