<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== LV Plans Details ===\n";

$lvPlans = \App\Models\Plan::whereHas('countries', function($q) {
    $q->where('code', 'LV');
})->with('countries')->get();

foreach ($lvPlans as $plan) {
    echo "Plan: " . $plan->name . "\n";
    echo "  Slug: " . $plan->slug . "\n";
    echo "  Price: " . $plan->retail_price . " " . $plan->price_currency . "\n";
    echo "  Capacity: " . $plan->capacity . " " . $plan->capacity_unit . "\n";
    echo "  Period: " . $plan->period . "\n";
    echo "  Countries: " . $plan->countries->pluck('code')->implode(', ') . "\n";
    echo "---\n";
}

echo "\n=== Total Plans by Country ===\n";
$countriesWithPlans = \App\Models\Country::whereHas('plans')->withCount('plans')->get();
foreach ($countriesWithPlans->take(10) as $country) {
    echo $country->code . ": " . $country->plans_count . " plans\n";
}




