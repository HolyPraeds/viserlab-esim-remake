<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Esim;

class EsimController extends Controller {

    public function all($userId = null) {
        $pageTitle = 'All eSIMs';
        $esims = $this->eSIMData(null, $userId);
        return view('admin.esim.index', compact('pageTitle', 'esims'));
    }
    public function active($userId = null) {
        $pageTitle = 'Active eSIMs';
        $esims = $this->eSIMData('active', $userId);
        return view('admin.esim.index', compact('pageTitle', 'esims'));
    }

    public function expired($userId = null) {
        $pageTitle = 'Expired eSIMs';
        $esims = $this->eSIMData('expired', $userId);
        return view('admin.esim.index', compact('pageTitle', 'esims'));
    }

    private function eSIMData($scope = null, $userId = null) {
        if ($scope) {
            $query = Esim::$scope();
        } else {
            $query = Esim::query();
        }

        if ($userId) {
            $query = $query->where('user_id', $userId);
        }

        return $query->dateFilter()->searchable(['phone_number', 'user:username'])->with('user', 'orderItem.plan')->orderBy('id', 'DESC')->paginate(getPaginate());
    }
}
