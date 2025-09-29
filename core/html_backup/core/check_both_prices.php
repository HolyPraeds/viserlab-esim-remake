<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Check both price columns
$plans = \App\Models\Plan::select('id', 'name', 'retail_price', 'price_currency', 'price')->limit(10)->get();

echo "Comparing both price columns:\n";
foreach ($plans as $plan) {
    echo "ID: {$plan->id}\n";
    echo "Name: {$plan->name}\n";
    echo "Retail Price (API): {$plan->retail_price} {$plan->price_currency}\n";
    echo "Price (old): {$plan->price}\n";
    echo "Difference: " . ($plan->retail_price - $plan->price) . "\n";
    echo "---\n";
}



