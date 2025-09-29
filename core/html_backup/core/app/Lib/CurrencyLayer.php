<?php

namespace App\Lib;

use App\Models\Currency;

class CurrencyLayer {
    public function getRates(array $currencies) {
        $apiKey       = gs('currency_api_key');
        $baseCurrrency = gs('cur_text');
        $currencyList = implode(',', $currencies);

        $url = "http://api.currencylayer.com/live?access_key={$apiKey}&currencies={$currencyList}&source={$baseCurrrency}&format=1";

        $response = CurlRequest::curlContent($url);
        $data     = json_decode($response, true);

        if ($data && isset($data['success']) && $data['success'] && isset($data['quotes'])) {
            return [
                'status' => true,
                'quotes' => $data['quotes']
            ];
        } else {
            return [
                'status' => false,
                'message' => 'API error'
            ];
        }
    }
    public function updateRates(array $currencies) {
        $response = $this->getRates($currencies);
        $baseCurrrency = gs('cur_text');


        if($response['status']){
            foreach ($response['quotes'] as $key => $rate) {
                $currencyCode = str_replace($baseCurrrency, '', $key);
                $currency = Currency::where('api_currency', $currencyCode)->first();
                if($currency){
                    $currency->conversion_rate = $rate;
                    $currency->save();
                }
            }
        }
    }
}
