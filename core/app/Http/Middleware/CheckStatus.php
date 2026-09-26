<?php

namespace App\Http\Middleware;

use App\Constants\Status;
use Closure;
use Auth;

class CheckStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $user = auth()->user();

            // Deposit / checkout / wallet payment: only require active account (not full ev/sv/tv),
            // otherwise local/test users get stuck on Authorization and "deposit doesn't work".
            $routeName = $request->route()?->getName() ?? '';
            $skipStrictVerification = $routeName !== '' && (
                str_starts_with($routeName, 'user.deposit')
                || str_starts_with($routeName, 'user.order.payment')
                || $routeName === 'user.order.pay.from.wallet'
                || $routeName === 'user.plan.purchase'
                || $routeName === 'user.plan.buy.from.wallet'
                || $routeName === 'user.purchase.index'
            );
            if ($skipStrictVerification && (int) $user->status === Status::USER_ACTIVE) {
                return $next($request);
            }

            if ($user->status  && $user->ev  && $user->sv  && $user->tv) {
                return $next($request);
            } else {
                if ($request->is('api/*')) {
                    $notify[] = 'You need to verify your account first. Please logout and re-login';
                    return response()->json([
                        'remark'=>'unverified',
                        'status'=>'error',
                        'message'=>['error'=>$notify],
                        'data'=>[
                            'user'=>$user
                        ],
                    ]);
                }else{
                    return to_route('user.authorization');
                }
            }
        }
        abort(403);
    }
}
