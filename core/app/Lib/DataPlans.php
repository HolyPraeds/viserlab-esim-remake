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

class DataPlans {
    private $baseUrl = 'https://api.esimaccess.com/api/v1/open';

    private function getHeader() {
        $apiKey = gs('plan_api');

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

            $plan->name             = $item['name'];
            $plan->period           = "{$item['duration']} {$item['durationUnit']}";
            $plan->capacity         = $item['volume'];
            $plan->capacity_unit    = "B";
            $plan->retail_price     = round($item['retailPrice'] / 1000, 2);
            $plan->price_currency   = strtoupper($item['currencyCode']);
            $plan->speed            = $item['speed'] ?? null;
            $plan->prepaid_credit   = round($item['retailPrice'] / 1000, 2);
            $plan->prepaid_currency = $item['currencyCode'];
            $plan->reloadable       = "true";
            $plan->phone_number     = "true";
            $plan->status           = 1;
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

        $user = $order->user;
        $orderItem = $order->orderItem;

        // Use dedicated EsimAccessService wrapper
        $response = app(\App\Services\EsimAccessService::class)->purchase($order);

        if (isset($response['error'])) {
            $this->refundForFailurePurchase($orderItem, $user);
            return [
                'status' => false,
                'message' => 'The plan is currently unavailable'
            ];
        }

        if (empty($response['purchase']) || empty($response['purchase']['esim'])) {
            $this->refundForFailurePurchase($orderItem, $user);
            return [
                'status' => false,
                'message' => 'The plan is currently unavailable'
            ];
        }

        $responseData = $response;

        $paidAmount  = $responseData['purchase']['paid'];
        $currency = Currency::where('api_currency', gs('payment_cur_text'))->first();
        if ($currency) {
            $paidAmount = $currency->baseCurrencyAmount($paidAmount);
        }

        $order->status = Status::ORDER_COMPLETED;
        $order->save();

        $orderItem->paid_price  = $paidAmount;
        $orderItem->purchase_id = $responseData['purchase']['purchaseId'];
        $orderItem->save();

        $esim                = new Esim();
        $esim->user_id       = $user->id;
        $esim->order_item_id = $orderItem->id;
        $esim->serial_number = $responseData['purchase']['esim']['serial'];
        $esim->phone_number  = $responseData['purchase']['esim']['phone'];
        $esim->qr_code       = $responseData['purchase']['esim']['qrCodeString'];
        $esim->expiry_date   = $responseData['purchase']['esim']['expiryDate'];
        $esim->save();

        // Update wallet and transactions depending on payment source
        if ($paidFromWallet) {
            $user->balance -= $order->total_amount;
            $user->save();

            $transaction               = new Transaction();
            $transaction->user_id      = $user->id;
            $transaction->order_id     = $orderItem->order_id;
            $transaction->amount       = $order->total_amount;
            $transaction->post_balance = $user->balance;
            $transaction->trx_type     = '-';
            $transaction->details      = 'Payment completed for order ' . $order->order_number . ' (wallet)';
            $transaction->trx          = getTrx();
            $transaction->remark       = 'order_payment_wallet';
            $transaction->save();
        } else {
            // For external gateway payments, do not touch wallet balance.
            // Optionally record a non-balance-affecting transaction for audit trail.
            $transaction               = new Transaction();
            $transaction->user_id      = $user->id;
            $transaction->order_id     = $orderItem->order_id;
            $transaction->amount       = $order->total_amount;
            $transaction->post_balance = $user->balance; // unchanged
            $transaction->trx_type     = '0'; // neutral
            $transaction->details      = 'Payment captured via gateway for order ' . $order->order_number;
            $transaction->trx          = getTrx();
            $transaction->remark       = 'order_payment_gateway';
            $transaction->save();
        }

        notify($user, 'PAYMENT_COMPLETED', [
            'order_number'  => $order->order_number,
            'plan'          => $orderItem->plan->name,
            'amount'        => showAmount($order->total_amount, currencyFormat: false),
            'trx'           => $transaction->trx,
        ]);

        $otherOrder = Order::where('user_id', $user->id)->where('id', '!=', $order->id)->exists();
        if(!$otherOrder){
            userReferralCommission($user);
        }

        return [
            'status' => true,
            'message' => 'Order completed successfully'
        ];
    }

    private function refundForFailurePurchase($orderItem, $user) {
        $user->balance += $orderItem->price;
        $user->save();

        $transaction               = new Transaction();
        $transaction->user_id      = $user->id;
        $transaction->order_id     = $orderItem->order_id;
        $transaction->amount       = $orderItem->price;
        $transaction->post_balance = $user->balance;
        $transaction->trx_type     = '+';
        $transaction->details      = 'Refund for failed to purchase ' . $orderItem->plan->name;
        $transaction->trx          = getTrx();
        $transaction->remark       = 'refund';
        $transaction->save();

        notify($user, 'PLAN_UNAVAILABLE', [
            'plan_price' => showAmount($orderItem->price, currencyFormat: false),
            'trx'        => $transaction->trx,
        ]);
    }
}
