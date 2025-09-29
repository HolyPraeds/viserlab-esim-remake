<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Plan extends Model {
    use GlobalStatus;

    protected $guarded = [];

    public function countries() {
        return $this->belongsToMany(Country::class);
    }

    public function api() {
        return $this->belongsTo(Api::class);
    }

    public function region() {
        return $this->belongsTo(Region::class);
    }

    public function order() {
        return $this->hasMany(Order::class);
    }

    public function currency() {
        return $this->belongsTo(Currency::class);
    }

    public function convertedPrice(): Attribute {
        return Attribute::make(
            get: function () {
                if ($this->price_currency == gs('cur_text')) {
                    return $this->retail_price;
                }

                if ($this->currency && $this->currency->conversion_rate > 0) {
                    return $this->retail_price / $this->currency->conversion_rate;
                }

                return $this->retail_price;
            }
        );
    }
}
