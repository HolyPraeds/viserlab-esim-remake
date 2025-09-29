<?php

namespace App\Http\Controllers\IPN;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EsimAccessController extends Controller
{
    public function ipn(Request $request)
    {
        $payload = $request->getContent();
        Log::info('EsimAccess IPN received', [
            'headers' => $request->headers->all(),
            'payload' => $payload,
        ]);

        return response('OK', 200);
    }
}




