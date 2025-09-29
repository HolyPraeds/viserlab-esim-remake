<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function index()
    {
        $pageTitle    = 'All Currency Rates';
        $currencies   = Currency::orderBy('id', 'DESC')->paginate(getPaginate());
        return view('admin.currency.index', compact('pageTitle', 'currencies'));
    }

    public function update($id, Request $request)
    {
        $request->validate([
            'conversion_rate' => 'required|numeric|gt:0',
        ]);

        $currency = Currency::findOrFail($id);
        $currency->conversion_rate = $request->conversion_rate;
        $currency->save();

        $notify[] = ['success', 'Currency updated successfully'];
        return back()->withNotify($notify);

    }

}
