<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model {
    public function baseCurrencyAmount($amount) {
        if ($this->conversion_rate > 0 && $this->api_currency != gs('cur_text')) {
            return $amount / $this->conversion_rate;
        }
        return $amount;
    }
}
