<?php

declare(strict_types=1);

use App\Models\Country;
use App\Models\Plan;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/../vendor/autoload.php';

/** @var \Illuminate\Foundation\Application $app */
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$targetSlugs = ['united-states', 'canada'];

$countries = Country::query()
    ->whereIn('slug', $targetSlugs)
    ->get(['id', 'slug', 'name']);

if ($countries->isEmpty()) {
    echo "No matching countries found for: " . implode(', ', $targetSlugs) . PHP_EOL;
    exit(0);
}

$countryIds = $countries->pluck('id')->all();
$planIds = DB::table('country_plan')
    ->whereIn('country_id', $countryIds)
    ->pluck('plan_id')
    ->unique()
    ->values();

if ($planIds->isEmpty()) {
    echo "No plans linked to target countries." . PHP_EOL;
    exit(0);
}

$hasPriceColumn = DB::getSchemaBuilder()->hasColumn('plans', 'price');

$plansQuery = Plan::query()->whereIn('id', $planIds);
$plans = $plansQuery->get(['id', 'name', 'retail_price', $hasPriceColumn ? 'price' : DB::raw('NULL as price'), 'status']);

$zeroPlans = $plans->filter(function ($plan) use ($hasPriceColumn) {
    $retail = (float) ($plan->retail_price ?? 0);
    $price = $hasPriceColumn ? (float) ($plan->price ?? 0) : $retail;
    return $retail <= 0 || $price <= 0;
});

if ($zeroPlans->isEmpty()) {
    echo "No zero-price plans found for USA/Canada." . PHP_EOL;
    exit(0);
}

DB::transaction(function () use ($zeroPlans) {
    Plan::query()
        ->whereIn('id', $zeroPlans->pluck('id'))
        ->update(['status' => 0]);
});

echo "Disabled " . $zeroPlans->count() . " zero-price plan(s): " . $zeroPlans->pluck('id')->implode(', ') . PHP_EOL;
