<?php

namespace App\Models\User;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Model;

class PurchasedPlan extends Model
{

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}
