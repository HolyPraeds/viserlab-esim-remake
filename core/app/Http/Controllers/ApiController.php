<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Region;
use App\Models\Plan;
use App\Constants\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ApiController extends Controller
{
    /**
     * Get all active countries with plans
     */
    public function countries()
    {
        try {
            $countries = Country::active()
                ->whereNotIn('code', stripeBlockedCountryCodes())
                ->with(['plans' => function ($query) {
                    $query->active()
                        ->withPositivePrice()
                        ->whereHas('region', fn($q) => $q->where('status', Status::ENABLE));
                }])
                ->get()
                ->filter(fn($country) => $country->plans->isNotEmpty())
                ->map(function ($country) {
                    $plan = $country->plans->sortBy('converted_price')->first();
                    return [
                        'id' => $country->id,
                        'name' => $country->name,
                        'code' => $country->code,
                        'slug' => $country->slug,
                        'image' => $country->image ? getImage(getFilePath('country') . '/' . $country->image, getFileSize('country')) : null,
                        'plans_count' => $country->plans->count(),
                        'min_price' => $plan?->converted_price ?? 0,
                        'currency' => $plan?->currency?->currency_code ?? 'USD'
                    ];
                })
                ->sortBy('name')
                ->values();

            return response()->json([
                'status' => 'success',
                'message' => 'Countries retrieved successfully',
                'data' => $countries
            ]);

        } catch (\Exception $e) {
            Log::error('API Error - countries: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve countries',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get plans for a specific country
     */
    public function countryPlans($slug)
    {
        try {
            $country = Country::with(['plans' => function ($query) {
                $query->active()
                    ->withPositivePrice()
                    ->whereHas('region', fn($q) => $q->where('status', Status::ENABLE));
            }])
            ->active()
            ->whereNotIn('code', stripeBlockedCountryCodes())
            ->where('slug', $slug)
            ->first();

            if (!$country) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Country not found'
                ], 404);
            }

            $plans = $country->plans->map(function ($plan) {
                return [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'capacity' => $plan->capacity,
                    'capacity_unit' => $plan->capacity_unit,
                    'period' => $plan->period,
                    'price' => planCustomerPrice($plan),
                    'converted_price' => $plan->converted_price,
                    'currency' => $plan->currency?->currency_code ?? 'USD',
                    'operator_name' => $plan->operator_name,
                    'operator_slug' => $plan->operator_slug,
                    'description' => $plan->description,
                    'features' => $plan->features ? json_decode($plan->features) : [],
                    'status' => $plan->status
                ];
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Country plans retrieved successfully',
                'data' => [
                    'country' => [
                        'id' => $country->id,
                        'name' => $country->name,
                        'code' => $country->code,
                        'slug' => $country->slug,
                        'image' => $country->image ? getImage(getFilePath('country') . '/' . $country->image, getFileSize('country')) : null
                    ],
                    'plans' => $plans
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('API Error - countryPlans: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve country plans',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all active regions with plans
     */
    public function regions()
    {
        try {
            $regions = Region::active()
                ->with(['plans' => function ($query) {
                    $query->active()
                        ->withPositivePrice()
                        ->whereHas('countries', fn($q) => $q->active()->whereNotIn('code', stripeBlockedCountryCodes()));
                }])
                ->get()
                ->filter(fn($region) => $region->plans->isNotEmpty())
                ->map(function ($region) {
                    $plan = $region->plans->sortBy('converted_price')->first();
                    return [
                        'id' => $region->id,
                        'name' => $region->name,
                        'slug' => $region->slug,
                        'description' => $region->description,
                        'plans_count' => $region->plans->count(),
                        'min_price' => $plan?->converted_price ?? 0,
                        'currency' => $plan?->currency?->currency_code ?? 'USD'
                    ];
                })
                ->sortBy('name')
                ->values();

            return response()->json([
                'status' => 'success',
                'message' => 'Regions retrieved successfully',
                'data' => $regions
            ]);

        } catch (\Exception $e) {
            Log::error('API Error - regions: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve regions',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get plans for a specific region
     */
    public function regionPlans($slug)
    {
        try {
            $region = Region::with(['plans' => function ($query) {
                $query->active()
                    ->withPositivePrice()
                    ->whereHas('countries', fn($q) => $q->active()->whereNotIn('code', stripeBlockedCountryCodes()));
            }])
            ->active()
            ->where('slug', $slug)
            ->first();

            if (!$region) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Region not found'
                ], 404);
            }

            $plans = $region->plans->map(function ($plan) {
                return [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'capacity' => $plan->capacity,
                    'capacity_unit' => $plan->capacity_unit,
                    'period' => $plan->period,
                    'price' => planCustomerPrice($plan),
                    'converted_price' => $plan->converted_price,
                    'currency' => $plan->currency?->currency_code ?? 'USD',
                    'operator_name' => $plan->operator_name,
                    'operator_slug' => $plan->operator_slug,
                    'description' => $plan->description,
                    'features' => $plan->features ? json_decode($plan->features) : [],
                    'status' => $plan->status
                ];
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Region plans retrieved successfully',
                'data' => [
                    'region' => [
                        'id' => $region->id,
                        'name' => $region->name,
                        'slug' => $region->slug,
                        'description' => $region->description
                    ],
                    'plans' => $plans
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('API Error - regionPlans: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve region plans',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search countries by name or code
     */
    public function searchCountries(Request $request)
    {
        try {
            $query = $request->get('q');
            
            if (!$query) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Search query is required'
                ], 400);
            }

            $countries = Country::active()
                ->whereNotIn('code', stripeBlockedCountryCodes())
                ->where(function($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('code', 'like', "%{$query}%");
                })
                ->with(['plans' => function ($query) {
                    $query->active()
                        ->withPositivePrice()
                        ->whereHas('region', fn($q) => $q->where('status', Status::ENABLE));
                }])
                ->get()
                ->filter(fn($country) => $country->plans->isNotEmpty())
                ->map(function ($country) {
                    $plan = $country->plans->sortBy('converted_price')->first();
                    return [
                        'id' => $country->id,
                        'name' => $country->name,
                        'code' => $country->code,
                        'slug' => $country->slug,
                        'image' => $country->image ? getImage(getFilePath('country') . '/' . $country->image, getFileSize('country')) : null,
                        'plans_count' => $country->plans->count(),
                        'min_price' => $plan?->converted_price ?? 0,
                        'currency' => $plan?->currency?->currency_code ?? 'USD'
                    ];
                })
                ->sortBy('name')
                ->values();

            return response()->json([
                'status' => 'success',
                'message' => 'Search results retrieved successfully',
                'data' => $countries
            ]);

        } catch (\Exception $e) {
            Log::error('API Error - searchCountries: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to search countries',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all active plans
     */
    public function allPlans()
    {
        try {
            $plans = Plan::active()
                ->withPositivePrice()
                ->whereHas('region', fn($q) => $q->where('status', Status::ENABLE))
                ->whereHas('countries', fn($q) => $q->active()->whereNotIn('code', stripeBlockedCountryCodes()))
                ->with(['countries', 'region', 'currency'])
                ->get()
                ->map(function ($plan) {
                    return [
                        'id' => $plan->id,
                        'name' => $plan->name,
                        'capacity' => $plan->capacity,
                        'capacity_unit' => $plan->capacity_unit,
                        'period' => $plan->period,
                        'price' => planCustomerPrice($plan),
                        'converted_price' => $plan->converted_price,
                        'currency' => $plan->currency?->currency_code ?? 'USD',
                        'operator_name' => $plan->operator_name,
                        'operator_slug' => $plan->operator_slug,
                        'description' => $plan->description,
                        'features' => $plan->features ? json_decode($plan->features) : [],
                        'countries' => $plan->countries->map(function ($country) {
                            return [
                                'id' => $country->id,
                                'name' => $country->name,
                                'code' => $country->code,
                                'slug' => $country->slug
                            ];
                        }),
                        'region' => $plan->region ? [
                            'id' => $plan->region->id,
                            'name' => $plan->region->name,
                            'slug' => $plan->region->slug
                        ] : null,
                        'status' => $plan->status
                    ];
                });

            return response()->json([
                'status' => 'success',
                'message' => 'All plans retrieved successfully',
                'data' => $plans
            ]);

        } catch (\Exception $e) {
            Log::error('API Error - allPlans: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve plans',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get specific plan details
     */
    public function planDetails($id)
    {
        try {
            $plan = Plan::active()
                ->withPositivePrice()
                ->whereHas('region', fn($q) => $q->where('status', Status::ENABLE))
                ->whereHas('countries', fn($q) => $q->active()->whereNotIn('code', stripeBlockedCountryCodes()))
                ->with(['countries', 'region', 'currency'])
                ->find($id);

            if (!$plan) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Plan not found'
                ], 404);
            }

            $planData = [
                'id' => $plan->id,
                'name' => $plan->name,
                'capacity' => $plan->capacity,
                'capacity_unit' => $plan->capacity_unit,
                'period' => $plan->period,
                'price' => planCustomerPrice($plan),
                'converted_price' => $plan->converted_price,
                'currency' => $plan->currency?->currency_code ?? 'USD',
                'operator_name' => $plan->operator_name,
                'operator_slug' => $plan->operator_slug,
                'description' => $plan->description,
                'features' => $plan->features ? json_decode($plan->features) : [],
                'countries' => $plan->countries->map(function ($country) {
                    return [
                        'id' => $country->id,
                        'name' => $country->name,
                        'code' => $country->code,
                        'slug' => $country->slug,
                        'image' => $country->image ? getImage(getFilePath('country') . '/' . $country->image, getFileSize('country')) : null
                    ];
                }),
                'region' => $plan->region ? [
                    'id' => $plan->region->id,
                    'name' => $plan->region->name,
                    'slug' => $plan->region->slug,
                    'description' => $plan->region->description
                ] : null,
                'status' => $plan->status
            ];

            return response()->json([
                'status' => 'success',
                'message' => 'Plan details retrieved successfully',
                'data' => $planData
            ]);

        } catch (\Exception $e) {
            Log::error('API Error - planDetails: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve plan details',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
