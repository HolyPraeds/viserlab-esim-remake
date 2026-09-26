<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RegistrationStep
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        // Allow checkout, deposits and wallet purchase without forcing profile wizard first
        // Note: user.deposit.* only matches one segment; user.deposit.alppay.create needs explicit prefixes.
        $routeName = $request->route()?->getName() ?? '';
        if ($routeName !== '' && (
            str_starts_with($routeName, 'user.deposit')
            || str_starts_with($routeName, 'user.order.payment')
            || $routeName === 'user.order.pay.from.wallet'
            || $routeName === 'user.plan.purchase'
            || $routeName === 'user.plan.buy.from.wallet'
            || $routeName === 'user.purchase.index'
        )) {
            return $next($request);
        }

        if (!$user->profile_complete) {
            if ($request->is('api/*')) {
                $notify[] = 'Please complete your profile to go next';
                return response()->json([
                    'remark'=>'profile_incomplete',
                    'status'=>'error',
                    'message'=>['error'=>$notify],
                ]);
            }else{
                return to_route('user.data');
            }
        }
        return $next($request);
    }
}
