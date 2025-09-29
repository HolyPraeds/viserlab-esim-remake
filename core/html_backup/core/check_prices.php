<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Check prices in database
$plans = \App\Models\Plan::select('id', 'name', 'retail_price', 'price_currency', 'converted_price')->limit(10)->get();

echo "Plans in database:\n";
foreach ($plans as $plan) {
    echo "ID: {$plan->id}\n";
    echo "Name: {$plan->name}\n";
    echo "Retail Price: {$plan->retail_price} {$plan->price_currency}\n";
    echo "Converted Price: {$plan->converted_price}\n";
    echo "---\n";
}

// Check currency settings
echo "\nCurrency settings:\n";
echo "Default currency: " . gs('cur_text') . "\n";
echo "Currency symbol: " . gs('cur_sym') . "\n";



