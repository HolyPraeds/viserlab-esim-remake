<?php

namespace App\Models;

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
}
