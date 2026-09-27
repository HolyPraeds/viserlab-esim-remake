<?php

namespace App\Http\Middleware;

use App\Support\BrandContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DetectBrand
{
    public function handle(Request $request, Closure $next): Response
    {
        $brand = BrandContext::resolve($request);
        BrandContext::apply($brand);
        BrandContext::applyRequestUrl($request);

        view()->share('currentBrand', $brand);

        return $next($request);
    }
}
