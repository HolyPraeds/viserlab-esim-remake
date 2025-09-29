<?php

namespace App\Http\Controllers;

use App\Models\Plan;

class EsimPackageController extends Controller
{
    public function showPlans()
    {
        $pageTitle = 'eSIM Plans';
        $regions   = Plan::select('location')->distinct()->pluck('location');

        $groupedPlans = [];

        foreach ($regions as $region) {
            $groupedPlans[$region] = Plan::where('location', $region)->get();
        }

        return view('Template::esim.packages', compact('groupedPlans', 'pageTitle'));
    }

    public function index()
    {
        $pageTitle = 'eSIM Plans';
        $regions   = Plan::select('location')->distinct()->orderBy('location', 'asc')->get()->pluck('location');
        $countries = Plan::select('location_network_list')->get()->flatMap(function ($plan) {
            return collect(json_decode($plan->select_network_list, true))->pluck('country');
        })->unique();

        $plans = Plan::all();

        return view('Template::esim.index', compact('plans', 'pageTitle', 'regions', 'countries'));
    }

    public function byRegion($region)
    {
        $pageTitle = 'eSIM Plans';
        $plans     = Plan::where('location', $region)->orderBy('name', 'asc')->get();

        return view('Template::esim.filtered', compact('plans', 'pageTitle'));
    }

    public function byCountry($country)
    {
        $pageTitle = 'eSIM Plans';
        $plans     = Plan::all()->filter(function ($plan) use ($country) {
            $networks = json_decode($plan->location_network_list, true);
            return collect($networks)->pluck('country')->contains($country) || collect($networks)->pluck('mcc')->contains($country);
        })->sortBy('name')->values();

        return view('Template::esim.filtered', compact('plans', 'pageTitle'));
    }
}
