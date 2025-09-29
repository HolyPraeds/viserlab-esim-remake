<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Lib\RequiredConfig;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function api()
    {
        $pageTitle      = 'Manage APIs';
        $apiKey         = gs()->plan_api;
        $currencyApiKey = gs()->currency_api_key;
        return view('admin.api.index', compact('pageTitle', 'apiKey', 'currencyApiKey'));
    }

    public function save(Request $request)
    {
        $request->validate([
            'api_key'          => 'required|string',
            'currency_api_key' => 'nullable|string',
        ]);

        $general                   = gs();
        $general->plan_api         = $request->api_key;
        $general->currency_api_key = $request->currency_api_key;

        $general->save();

        RequiredConfig::configured('api_keys');

        $notify[] = ['success', 'API updated successfully'];
        return back()->withNotify($notify);

    }
}
