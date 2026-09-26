<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Esim extends Model
{
    use GlobalStatus;
    
    protected $fillable = [
        'user_id',
        'plan_id', 
        'order_item_id',
        'serial_number',
        'phone_number',
        'qr_code',
        'expiry_date'
    ];
    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Active = not yet expired. Include null expiry so eSIM always shows until we have a date. */
    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expiry_date')->orWhereDate('expiry_date', '>=', now());
        });
    }

    public function scopeExpired($query){
        return $query->whereDate('expiry_date', '<', now());
    }

    public function statusBadge(): Attribute
    {
        return new Attribute(function () {
            $html = '';
            if ($this->status == Status::PLAN_ENABLE && $this->expiry_date < now()) {
                $html = '<span class="badge badge--danger">' . trans('Expired') . '</span>';
            } else if ($this->status == Status::PLAN_ENABLE) {
                $html = '<span class="badge badge--success">' . trans('Active') . '</span>';
            } else if ($this->status == Status::PLAN_PENDING) {
                $html = '<span class="badge badge--warning">' . trans('Pending') . '</span>';
            }
            return $html;
        });
    }
}
