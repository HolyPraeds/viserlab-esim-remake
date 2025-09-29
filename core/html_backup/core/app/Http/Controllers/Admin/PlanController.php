<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\RequiredConfig;
use App\Models\Currency;
use App\Models\Plan;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PlanController extends Controller {
    public function plans() {
        $pageTitle = 'All Plans';
        $plans     = Plan::with('region', 'currency')->latest('id')->filter(['status'])->searchable(['name']);

        if (request()->country) {
            $plans->whereHas('countries', function ($q) {
                $q->where('countries.slug', request()->country);
            });
        }

        if (request()->region) {
            $plans->whereHas('region', function ($q) {
                $q->where('regions.slug', request()->region);
            });
        }

        $plans = $plans->paginate(getPaginate($this->getPageNumber()));
        $currencies = Currency::all();
        return view('admin.plan.index', compact('pageTitle', 'plans', 'currencies'));
    }
    public function detail($id) {
        $pageTitle = 'Plan Details';
        $plan      = Plan::with('countries', 'region')->findOrFail($id);
        return view('admin.plan.details', compact('pageTitle', 'plan'));
    }

    public function local() {
        return $this->getPlansByRegion('local', 'Local Plans');
    }

    public function global() {
        return $this->getPlansByRegion('global', 'Global Plans');
    }

    public function continental() {
        return $this->getPlansByRegion(null, 'Continental Plans', ['local', 'global']);
    }

    private function getPlansByRegion($slug = null, $pageTitle = 'Plans', $excludeRegionSlugs = []) {
        $query = Plan::with('countries', 'region')->latest('id');

        if ($slug) {
            $region = Region::where('slug', $slug)->first();
            if ($region) {
                $query->where('region_id', $region->id);
            } else {
                $query->where('region_id', null);
                notify('error', "Region not found.");
            }
        } elseif ($excludeRegionSlugs) {
            $excludeRegionIds = Region::whereIn('slug', $excludeRegionSlugs)->pluck('id')->toArray();
            if ($excludeRegionIds) {
                $query->whereNotIn('region_id', $excludeRegionIds);
            } else {
                $query->where('region_id', null);
                notify('warning', 'No regions found to exclude.');
            }
        }

        $plans = $query->paginate(getPaginate($this->getPageNumber()));
        $currencies = Currency::all();
        return view('admin.plan.index', compact('pageTitle', 'plans', 'currencies'));
    }

    private function getPageNumber() {
        $number = null;
        if (in_array(request()->per_page, [20, 50, 100, 150, 200])) {
            $number = request()->per_page;
        }

        return $number;
    }

    public function changeStatus(Request $request) {
        $flatPlans = implode(',', $request->plans);
        $plans     = explode(',', $flatPlans);
        $request->merge(['plans' => $plans]);

        $request->validate([
            'plans'  => 'required|array|min:1',
            'status' => ['required', Rule::in([Status::ENABLE, Status::DISABLE])],
        ]);

        $plans = Plan::whereIn('id', $request->plans)->update(
            ['status' => $request->status]
        );

        $notify[] = ['success', 'Status updated successfully'];
        return back()->withNotify($notify);
    }

    public function fetchPlans() {
        $dataPlan = dataPlans();
        $plans = $dataPlan->fetchPlans();

        if (isset($plans['error'])) {
            $notify[] = ['error', $plans['error']];
            return back()->withNotify($notify);
        }

        $dataPlan->addOrUpdatePlans($plans);

        RequiredConfig::configured('plan');

        $notify[] = ['success', 'Plans fetched and inserted successfully'];
        return back()->withNotify($notify);
    }

    public function updateRetailPrice(Request $request, $id) {
        $plan = Plan::findOrFail($id);
        $request->validate([
            'retail_price' => 'required|numeric|min:0',
        ]);

        $plan->retail_price = $request->retail_price;
        $plan->save();

        $notify[] = ['success', 'Retail price updated successfully'];
        return back()->withNotify($notify);
    }
}
