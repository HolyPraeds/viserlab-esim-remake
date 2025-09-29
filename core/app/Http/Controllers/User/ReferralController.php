<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;

class ReferralController extends Controller
{
    public function index()
    {
        $pageTitle = "Referral History";
        $user      = auth()->user();
        $referrals = User::where('ref_by', $user->id)->orderBy('id', 'desc')->paginate(getPaginate());

        $widget['total_referrals'] = $referrals->count();
        $widget['total_earning']   = Transaction::where('user_id', $user->id)->where('remark', 'referral_commission')->sum('amount');

        $link = route('home', ['reference' => $user->username]);
        return view('Template::user.referral.index', compact('pageTitle', 'referrals', 'link', 'widget'));
    }

}
