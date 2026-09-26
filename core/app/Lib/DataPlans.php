<?php

namespace App\Lib;

use App\Constants\Status;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Esim;
use App\Models\Order;
use App\Models\Plan;
use App\Models\Region;
use App\Models\Transaction;
use App\Services\OrderEmailService;
use Illuminate\Support\Facades\DB;

class DataPlans {
    private $baseUrl = 'https://api.esimaccess.com/api/v1/open';

    private function convertToCredits(float $amount, string $currency): float
    {
        $currency = strtoupper(trim($currency));
        return match ($currency) {
            'GBP' => round($amount / 0.87, 2),
            'USD' => round($amount / 1.18, 2),
            default => round($amount, 2),
        };
    }

    private function getHeader() {
        $apiKey = config('services.esim.access_code') ?: gs('plan_api');

        return [
            "RT-AccessCode: $apiKey",
            'Content-Type: application/json',
        ];
    }

    public function fetchCountries() {
        $countriesPostData = [
            'locationCode' => ''
        ];
        try {
            $response = CurlRequest::curlPostContent($this->baseUrl . '/location/list', json_encode($countriesPostData) , $this->getHeader());
            $response = json_decode($response, true);
        } catch (\Exception $e) {
            return ['error' => 'Failed to fetch countries: ' . $e->getMessage()];
        }

        if (isset($response['error'])) {
            return ['error' => 'Failed to fetch countries: ' . $response['error']];
        }

        // if (isset($response['obj']['locationList']) && is_array($response['obj']['locationList'])) {
        //     $filtered = array_filter($response['obj']['locationList'], function($item) {
        //         return isset($item['type']) && $item['type'] == 1;
        //     });
        //     $response['obj']['locationList'] = array_values($filtered);
        // }

        // return $response;
        return array_values(array_filter(
            $response['obj']['locationList'],
            fn($item) => isset($item['type']) && (int)$item['type'] === 1
        ));
    }
    public function fetchRegions() {
        $regionsPostData = [
            'locationCode' => ''
        ];
        try {
            $response = CurlRequest::curlPostContent($this->baseUrl . '/location/list', json_encode($regionsPostData) , $this->getHeader());
            $response = json_decode($response, true);
        } catch (\Exception $e) {
            return ['error' => 'Failed to fetch countries: ' . $e->getMessage()];
        }

        if (isset($response['error'])) {
            return ['error' => 'Failed to fetch countries: ' . $response['error']];
        }

        // if (isset($response['obj']['locationList']) && is_array($response['obj']['locationList'])) {
        //     $filtered = array_filter($response['obj']['locationList'], function($item) {
        //         return isset($item['type']) && $item['type'] == 2;
        //     });
        //     $response['obj']['locationList'] = array_values($filtered);
        // }

        // return $response;

        return array_values(array_filter(
            $response['obj']['locationList'],
            fn($item) => isset($item['type']) && (int)$item['type'] === 2
        ));
    }
    public function fetchPlans() {
        $plansPostData = [
            'locationCode' => ''
        ];
        try {
            $response = CurlRequest::curlPostContent($this->baseUrl . '/package/list', json_encode($plansPostData) , $this->getHeader());
            $response = json_decode($response, true);
        } catch (\Exception $e) {
            return ['error' => 'Failed to fetch countries: ' . $e->getMessage()];
        }

        if (isset($response['error'])) {
            return ['error' => 'Failed to fetch countries: ' . $response['error']];
        }

        return array_values(
            $response['obj']['packageList']
        );
    }

    public function purchasePlans($plan) {
        try {
            $postData = [
                'slug' => $plan->slug,
            ];
            $response = CurlRequest::curlPostContent($this->baseUrl . '/purchases', $postData, $this->getHeader());
            $response = json_decode($response, true);
        } catch (\Exception $e) {
            return ['error' => 'Failed to purchase plan: ' . $e->getMessage()];
        }

        if (isset($response['error'])) {
            return ['error' => 'Failed to purchase plan: ' . $response['error']];
        }

        return $response;
    }

    public function remainingCapacity($operatorSlug, $phoneNumber) {
        try {
            $url      = $this->baseUrl . "/status/{$operatorSlug}/{$phoneNumber}";
            $response = CurlRequest::curlContent($url, $this->getHeader());
            $response = json_decode($response, true);
        } catch (\Exception $e) {
            return ['error' => 'Failed to fetch remaining capacity: ' . $e->getMessage()];
        }
        if (isset($response['error'])) {
            return ['error' => 'Failed to fetch remaining capacity: ' . $response['error']];
        }
        return $response;
    }

    public function fetchSinglePlan($planSlug) {
        try {
            $url      = $this->baseUrl . "/plan/{$planSlug}";
            $response = CurlRequest::curlContent($url, $this->getHeader());
            $response = json_decode($response, true);
        } catch (\Exception $e) {
            return ['error' => 'Failed to fetch plan: ' . $e->getMessage()];
        }

        if (isset($response['error'])) {
            return ['error' => 'Failed to fetch plan: ' . $response['error']];
        }

        return $response;
    }

    // add or update plan
    public function addOrUpdatePlans($plans) {

        $newCurrencyArray = [];
        foreach ($plans as $item) {
            // Очищаем slug от слешей и других проблемных символов
            $cleanSlug = str_replace(['\\', '/', ' '], ['_', '_', '_'], $item['slug']);
            
            $plan = Plan::where('slug', $cleanSlug)->first();

            if (!$plan) {
                $plan = new Plan();
                $plan->slug = $cleanSlug;
            }

            $currencyCode = strtoupper($item['currencyCode']);
            if ($currencyCode != gs('cur_text')) {
                $currency = Currency::where('api_currency', $currencyCode)->first();

                if (!$currency) {
                    $currency                  = new Currency();
                    $currency->api_currency    = $currencyCode;
                    $currency->conversion_rate = null;
                    $currency->save();

                    $newCurrencyArray[] = $currencyCode;
                }
                $plan->currency_id = $currency->id;
            } else {
                $plan->currency_id = null;
            }

            // Очищаем locationCode тоже
            $cleanLocationCode = str_replace(['\\', '/', ' '], ['_', '_', '_'], $item['locationCode']);
            
            $region = Region::where('slug', $cleanLocationCode)->first();

            if (!$region) {
                $region = Region::whereJsonContains('countries_list', $cleanLocationCode)->first();
            }

            $retailPrice = isset($item['retailPrice']) ? (float) $item['retailPrice'] : 0;
            $apiRetailEur = round($retailPrice / 1000, 2);
            // Status must use stored EUR price after /1000 + customer divisor.
            // Raw API retailPrice of e.g. 1–9 becomes 0.00 EUR and must stay inactive.
            $divisor = max(1.0, (float) config('plans.customer_price_divisor', 4));
            $customerPrice = round($apiRetailEur / $divisor, 2);

            $plan->name             = $item['name'];
            $plan->period           = "{$item['duration']} {$item['durationUnit']}";
            $plan->capacity         = $item['volume'];
            $plan->capacity_unit    = "B";
            // Store provider list price; customer price = planCustomerPrice() (÷ customer_price_divisor).
            $plan->retail_price     = $apiRetailEur;
            $plan->price_currency   = strtoupper($item['currencyCode']);
            $plan->speed            = $item['speed'] ?? null;
            $plan->prepaid_credit   = $apiRetailEur;
            $plan->prepaid_currency = $item['currencyCode'];
            $plan->reloadable       = "true";
            $plan->phone_number     = "true";
            $plan->status           = ($customerPrice >= 0.01) ? 1 : 0;
            $plan->operator_name    = "UNDEFINED";
            $plan->operator_slug    = "undefined";
            $plan->region_id        = $region ? $region->id : null;
            $plan->save();

            $countryIds = [];

            // Only attach to a country if locationCode is a 2-letter country code (ISO alpha-2)
            $locationCode = strtoupper($cleanLocationCode);
            $isCountryCode = (bool) preg_match('/^[A-Z]{2}$/', $locationCode);

            if ($isCountryCode) {
                $countryModel = Country::where('code', $locationCode)->active()->first();
                if ($countryModel) {
                    $countryIds[] = $countryModel->id;
                }
            }

            // For regional codes (e.g., EU-42, AS-20), do not attach to all countries; keep linkage empty
            $plan->countries()->sync($countryIds);
        }

        $currencyLayer = new CurrencyLayer();
        $currencyLayer->updateRates($newCurrencyArray);
    }

    // confirm order plan purchase
    public function confirmPurchase($order, bool $paidFromWallet = false) {
        $order = $order->fresh(['user', 'orderItem.plan']) ?? $order;
        $lockName = 'esim_confirm_purchase_' . (int) $order->id;
        $lockRow = DB::selectOne("SELECT GET_LOCK(?, 0) AS acquired", [$lockName]);
        $acquired = (int) ($lockRow->acquired ?? 0) === 1;

        if (!$acquired) {
            $freshOrder = Order::find($order->id);
            if ($freshOrder && (int) $freshOrder->status === Status::ORDER_COMPLETED) {
                return ['status' => true, 'message' => 'Order already completed'];
            }
            \Illuminate\Support\Facades\Log::info('confirmPurchase: skipped duplicate concurrent call', [
                'order_id' => $order->id,
                'paid_from_wallet' => $paidFromWallet,
            ]);
            return ['status' => true, 'message' => 'Order is being processed'];
        }

        try {
            $order = $order->fresh(['user', 'orderItem.plan']) ?? $order;
            $user = $order->user;
            $orderItem = $order->orderItem;

            // Idempotent: avoid duplicate provider purchase if already fulfilled
            if ((int) $order->status === Status::ORDER_COMPLETED) {
                return ['status' => true, 'message' => 'Order already completed'];
            }

            // Use dedicated EsimAccessService wrapper
            $response = app(\App\Services\EsimAccessService::class)->purchase($order);

            if (isset($response['error'])) {
                \Illuminate\Support\Facades\Log::warning('EsimAccess purchase failed', [
                    'order_id' => $order->id,
                    'plan_slug' => $orderItem->plan->slug ?? null,
                    'error' => $response['error'],
                    'errorCode' => $response['errorCode'] ?? null,
                ]);
                $this->refundForFailurePurchase($orderItem, $user, $paidFromWallet);
                $userMessage = $response['error'];
                if (strlen($userMessage) > 120) {
                    $userMessage = 'The plan is currently unavailable. Please try another plan or contact support.';
                }
                return [
                    'status'  => false,
                    'message' => $userMessage,
                ];
            }

            // eSIM Access may return purchase in response or inside obj
            $purchase = $response['purchase'] ?? $response['obj']['purchase'] ?? $response['obj'] ?? null;
            if (empty($purchase['esim'])) {
                \Illuminate\Support\Facades\Log::warning('EsimAccess purchase response missing esim', [
                    'order_id' => $order->id,
                    'response_keys' => array_keys($response),
                ]);
                $this->refundForFailurePurchase($orderItem, $user, $paidFromWallet);
                return [
                    'status'  => false,
                    'message' => 'The plan is currently unavailable. Please try another plan or contact support.',
                ];
            }

            return $this->completeOrderWithEsimData($order, ['purchase' => $purchase], $paidFromWallet);
        } finally {
            DB::selectOne("SELECT RELEASE_LOCK(?)", [$lockName]);
        }
    }

    /**
     * Complete order with eSIM data (from purchase() or from webhook ORDER_STATUS GOT_RESOURCE).
     * Marks order completed, creates Esim record, deducts wallet if paidFromWallet, notifies user.
     */
    public function completeOrderWithEsimData($order, array $purchaseData, bool $paidFromWallet = false): array
    {
        $user = $order->user;
        $orderItem = $order->orderItem;

        $paidAmount = $purchaseData['purchase']['paid'] ?? 0;
        $currency = Currency::where('api_currency', gs('payment_cur_text'))->first();
        if ($currency) {
            $paidAmount = $currency->baseCurrencyAmount($paidAmount);
        }

        $order->status = Status::ORDER_COMPLETED;
        $order->save();

        $orderItem->paid_price  = $paidAmount;
        $orderItem->purchase_id = $purchaseData['purchase']['purchaseId'] ?? '';
        $orderItem->save();

        $purchase = $purchaseData['purchase'];
        $esimList = $purchase['esimList'] ?? null;
        if (!is_array($esimList) || empty($esimList)) {
            $esimList = isset($purchase['esim']) ? [$purchase['esim']] : [];
        }
        $createdEsims = [];
        foreach ($esimList as $esimData) {
            $qrRaw = $esimData['qrCodeString'] ?? '';
            $esim                = new Esim();
            $esim->user_id       = $user?->id ?? 0;
            $esim->order_item_id = $orderItem->id;
            $esim->serial_number = $esimData['serial'] ?? '';
            $esim->phone_number  = $esimData['phone'] ?? '';
            $esim->qr_code       = str_starts_with($qrRaw ?? '', 'http') ? stripPngFromUrl($qrRaw) : $qrRaw;
            $esim->expiry_date   = $esimData['expiryDate'] ?? null;
            $esim->save();
            $createdEsims[] = $esim;
        }
        $esim = $createdEsims[0] ?? null;
        if (!$esim) {
            return ['status' => false, 'message' => 'No eSIM data to save'];
        }

        // Persist transaction & wallet only when we have a real user account
        if ($user && $user->id) {
            if ($paidFromWallet) {
                $walletAmountCredits = $this->convertToCredits((float) $order->total_amount, (string) ($order->payment_currency ?? 'EUR'));
                $user->balance -= $walletAmountCredits;
                $user->save();
                $transaction               = new Transaction();
                $transaction->user_id      = $user->id;
                $transaction->order_id     = $orderItem->order_id;
                $transaction->amount       = $walletAmountCredits;
                $transaction->post_balance = $user->balance;
                $transaction->trx_type     = '-';
                $transaction->details      = 'Payment completed for order ' . $order->order_number . ' (wallet)';
                $transaction->trx          = getTrx();
                $transaction->remark       = 'order_payment_wallet';
                $transaction->save();
            } else {
                $transaction               = new Transaction();
                $transaction->user_id      = $user->id;
                $transaction->order_id     = $orderItem->order_id;
                $transaction->amount       = $order->total_amount;
                $transaction->post_balance = $user->balance;
                $transaction->trx_type     = '0';
                $transaction->details      = 'Payment captured via gateway for order ' . $order->order_number;
                $transaction->trx          = getTrx();
                $transaction->remark       = 'order_payment_gateway';
                $transaction->save();
            }
        } else {
            $transaction = null;
        }

        app(OrderEmailService::class)->sendPaymentCompleted(
            $order,
            null,
            $transaction->trx ?? ($purchaseData['purchase']['purchaseId'] ?? $order->order_number)
        );

        if ($user && $user->id) {
            $otherOrder = Order::where('user_id', $user->id)->where('id', '!=', $order->id)->exists();
            if (!$otherOrder) {
                userReferralCommission($user);
            }
        }

        return ['status' => true, 'message' => 'Order completed successfully'];
    }

    /**
     * Create only Esim records from purchase data (no order status, wallet, transaction, email).
     * Used when repairing completed orders that have no Esims (e.g. after email was sent but save failed).
     */
    public function createEsimRecordsOnly($order, array $purchaseData): bool
    {
        $user = $order->user;
        $orderItem = $order->orderItem;
        if (!$orderItem) {
            return false;
        }
        if (Esim::where('order_item_id', $orderItem->id)->exists()) {
            return true;
        }
        $purchase = $purchaseData['purchase'] ?? null;
        if (!$purchase) {
            return false;
        }
        $esimList = $purchase['esimList'] ?? null;
        if (!is_array($esimList) || empty($esimList)) {
            $esimList = isset($purchase['esim']) ? [$purchase['esim']] : [];
        }
        if (empty($esimList)) {
            return false;
        }
        foreach ($esimList as $esimData) {
            $qrRaw = $esimData['qrCodeString'] ?? '';
            $esim                = new Esim();
            $esim->user_id       = $user->id;
            $esim->order_item_id = $orderItem->id;
            $esim->serial_number = $esimData['serial'] ?? '';
            $esim->phone_number  = $esimData['phone'] ?? '';
            $esim->qr_code       = str_starts_with($qrRaw ?? '', 'http') ? stripPngFromUrl($qrRaw) : $qrRaw;
            $esim->expiry_date   = $esimData['expiryDate'] ?? null;
            $esim->save();
        }
        if (!empty($purchase['purchaseId'])) {
            $orderItem->purchase_id = $purchase['purchaseId'];
            $orderItem->save();
        }
        return true;
    }

    /**
     * For a user, find completed orders that have no Esim records but have purchase_id,
     * fetch profiles from API and create Esim records so eSIM always appears in dashboard.
     */
    public function repairMissingEsimsForUser($user): void
    {
        $orders = Order::with('orderItem')
            ->where('user_id', $user->id)
            ->where('status', Status::ORDER_COMPLETED)
            ->get();
        $esimService = app(\App\Services\EsimAccessService::class);
        foreach ($orders as $order) {
            $orderItem = $order->orderItem;
            if (!$orderItem) {
                continue;
            }
            $existingCount = Esim::where('order_item_id', $orderItem->id)->count();
            if ($existingCount > 0) {
                continue;
            }
            $orderNo = $orderItem->purchase_id ?: null;
            if (!$orderNo || $orderNo === '0') {
                continue;
            }
            $result = $esimService->fetchProfilesByOrderNo((string) $orderNo);
            if (isset($result['error'])) {
                continue;
            }
            $this->createEsimRecordsOnly($order, $result);
        }
    }

    private function refundForFailurePurchase($orderItem, $user, bool $paidFromWallet = false) {
        // Wallet is charged only after successful provider purchase.
        // So on failure there is usually nothing to refund.
        if (!$paidFromWallet || !$user || !$user->id) {
            return;
        }

        // Extra safety: refund only when a wallet-debit transaction for this order exists.
        $walletDebit = Transaction::where('user_id', $user->id)
            ->where('order_id', $orderItem->order_id)
            ->where('remark', 'order_payment_wallet')
            ->where('trx_type', '-')
            ->latest('id')
            ->first();

        if (!$walletDebit) {
            return;
        }

        $refundAmount = (float) $walletDebit->amount;
        $user->balance += $refundAmount;
        $user->save();

        $transaction               = new Transaction();
        $transaction->user_id      = $user->id;
        $transaction->order_id     = $orderItem->order_id;
        $transaction->amount       = $refundAmount;
        $transaction->post_balance = $user->balance;
        $transaction->trx_type     = '+';
        $transaction->details      = 'Refund for failed to purchase ' . $orderItem->plan->name;
        $transaction->trx          = getTrx();
        $transaction->remark       = 'refund';
        $transaction->save();

        notify($user, 'PLAN_UNAVAILABLE', [
            'plan_price' => showAmount($refundAmount, currencyFormat: false),
            'trx'        => $transaction->trx,
        ]);
    }
}
