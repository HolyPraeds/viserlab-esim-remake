<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Esim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EsimController extends Controller {
    public function active() {
        // Repair: ensure completed orders that have purchase_id but no Esims get eSIM records from API
        dataPlans()->repairMissingEsimsForUser(auth()->user());

        $pageTitle = 'Active eSIMs';
        $esims = Esim::active()->where('user_id', auth()->id())->with('orderItem.plan', 'orderItem.order')->orderBy('id', 'DESC')->paginate(getPaginate());
        return view('Template::user.esims', compact('pageTitle', 'esims'));
    }

    public function expired() {
        $pageTitle = 'Expired eSIMs';
        $esims = Esim::expired()->where('user_id', auth()->id())->with('orderItem.plan', 'orderItem.order')->orderBy('id', 'DESC')->paginate(getPaginate());
        return view('Template::user.esims', compact('pageTitle', 'esims'));
    }

    public function getQrCode($id) {
        $esim = Esim::active()->where('user_id', auth()->id())->findOrFail($id);
        $qr = (str_starts_with($esim->qr_code ?? '', 'http')) ? stripPngFromUrl($esim->qr_code) : cryptoQR($esim->qr_code);

        return response()->json([
            'status' => 'ACTIVE',
            'qr'     => $qr,
        ]);
    }


    public function checkCapacity(Request $request) {

        $validator = Validator::make($request->all(), [
            'esim_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->all(),
            ]);
        }

        $esim = Esim::with('orderItem.plan')->where('user_id', auth()->id())->find($request->esim_id);
        if (!$esim) {
            return response()->json([
                'status'  => 'error',
                'message' => 'eSIM not found.',
            ]);
        }

        $operatorSlug = $esim->orderItem->plan->operator_slug;
        $phoneNumber  = $esim->phone_number;

        $response     = dataPlans()->remainingCapacity($operatorSlug, $phoneNumber);
        if (isset($response['error'])) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Something went wrong.',
            ]);
        }

        $plan = $response['plans'][0] ?? null;

        if (!$plan) {
            return response()->json([
                'status'  => 'error',
                'message' => 'No plan data found in response.',
            ]);
        }
        return response()->json([
            'status' => 'success',
            'data'   => [
                'remaining'     => $plan['remainingCapacity'] . ' ' . $plan['capacityUnit'],
                'plan_expiry'   => showDateTime($plan['expiryDate'], 'd M Y, h:i A'),
                'esim_expiry'   => showDateTime($response['esim']['expiryDate'], 'd M Y, h:i A'),
                'phone'         => $phoneNumber,
            ],
        ]);
    }
}
