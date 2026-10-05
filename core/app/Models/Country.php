<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use GlobalStatus;
    protected $guarded = [];

    public function plans()
    {
        return $this->belongsToMany(Plan::class);
    }

    public function scopeHavingSellablePlans($query)
    {
        return $query->whereHas('plans', function ($planQuery) {
            self::constrainSellablePlans($planQuery);
        });
    }

    public static function constrainSellablePlans($planQuery): void
    {
        $planQuery->active()
            ->withPositivePrice()
            ->whereHas('region', fn ($regionQuery) => $regionQuery->where('status', Status::ENABLE));
    }
}
