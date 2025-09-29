<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'user/plan/purchase',
        'user/order/payment/initiate',
        'user/order/payment/taurixy-direct',
        'user/deposit/*',
        'payment/*',
        'webhook/*',
        'api/*',
    ];
}
