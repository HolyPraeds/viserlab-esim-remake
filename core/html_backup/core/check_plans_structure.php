<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Check plans table structure
$columns = \Illuminate\Support\Facades\Schema::getColumnListing('plans');
echo "Plans table columns: " . implode(', ', $columns) . "\n\n";

// Check some plans with available columns
$plans = \App\Models\Plan::select('id', 'name', 'retail_price', 'price_currency')->limit(5)->get();

echo "Sample plans:\n";
foreach ($plans as $plan) {
    echo "ID: {$plan->id}\n";
    echo "Name: {$plan->name}\n";
    echo "Retail Price: {$plan->retail_price} {$plan->price_currency}\n";
    echo "---\n";
}



