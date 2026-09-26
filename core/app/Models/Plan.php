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

    /**
     * Plans with a positive customer-facing price (after retail_price ÷ divisor).
     */
    public function scopeWithPositivePrice($query)
    {
        $divisor = max(1.0, (float) config('plans.customer_price_divisor', 4));
        $minRetail = 0.005 * $divisor;

        return $query->where('retail_price', '>=', $minRetail);
    }

    public function convertedPrice(): Attribute {
        return Attribute::make(
            get: function () {
                $customerPrice = planCustomerPrice($this);

                if ($this->price_currency == gs('cur_text')) {
                    return $customerPrice;
                }

                if ($this->currency && $this->currency->conversion_rate > 0) {
                    return $customerPrice / $this->currency->conversion_rate;
                }

                return $customerPrice;
            }
        );
    }
}
