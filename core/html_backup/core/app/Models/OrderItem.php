<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use GlobalStatus;

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function esim(){
        return $this->hasOne(Esim::class);
    }
}
