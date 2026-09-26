<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Model;

class GatewayCurrency extends Model
{
    protected $fillable = [
        'name',
        'currency',
        'symbol',
        'method_code',
        'gateway_alias',
        'min_amount',
        'max_amount',
        'percent_charge',
        'fixed_charge',
        'rate',
        'gateway_parameter',
    ];

    protected $hidden = [
        'gateway_parameter'
    ];

    protected $casts = ['status' => 'boolean'];

    // Relation
    public function method()
    {
        return $this->belongsTo(Gateway::class, 'method_code', 'code');
    }

    public function currencyIdentifier()
    {
        return $this->name ?? $this->method->name . ' ' . $this->currency;
    }

    public function scopeBaseCurrency()
    {
        return $this->method->crypto == Status::ENABLE ? 'USD' : $this->currency;
    }

    public function scopeBaseSymbol()
    {
        return $this->method->crypto == Status::ENABLE ? '$' : $this->symbol;
    }

}
