<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    use GlobalStatus;

    public function plans()
    {
        return $this->hasMany(Plan::class);
    }
}
