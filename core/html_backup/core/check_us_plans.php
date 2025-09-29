<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== US Plans Details ===\n";

$usPlans = \App\Models\Plan::whereHas('countries', function($q) {
    $q->where('code', 'US');
})->with('countries')->get();

echo "Found " . $usPlans->count() . " plans for US\n\n";

foreach ($usPlans as $plan) {
    echo "Plan: " . $plan->name . "\n";
    echo "  Slug: " . $plan->slug . "\n";
    echo "  Price: " . $plan->retail_price . " " . $plan->price_currency . "\n";
    echo "  Capacity: " . $plan->capacity . " bytes (" . round($plan->capacity / 1073741824, 2) . " GB)\n";
    echo "  Capacity Unit: " . $plan->capacity_unit . "\n";
    echo "  Period: " . $plan->period . "\n";
    echo "  Speed: " . ($plan->speed ?: 'N/A') . "\n";
    echo "  Countries: " . $plan->countries->pluck('code')->implode(', ') . "\n";
    echo "---\n";
}

echo "\n=== Checking for template data ===\n";
echo "Plans with capacity_unit != 'B':\n";
$nonStandardPlans = \App\Models\Plan::whereHas('countries', function($q) {
    $q->where('code', 'US');
})->where('capacity_unit', '!=', 'B')->get();

foreach ($nonStandardPlans as $plan) {
    echo "  - " . $plan->name . " (unit: " . $plan->capacity_unit . ")\n";
}

echo "\nPlans with empty speed:\n";
$noSpeedPlans = \App\Models\Plan::whereHas('countries', function($q) {
    $q->where('code', 'US');
})->whereNull('speed')->get();

foreach ($noSpeedPlans as $plan) {
    echo "  - " . $plan->name . " (speed: NULL)\n";
}




