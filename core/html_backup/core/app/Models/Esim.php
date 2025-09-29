<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Esim extends Model
{
    use GlobalStatus;
    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query){
        return $query->whereDate('expiry_date', '>=', now());
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
