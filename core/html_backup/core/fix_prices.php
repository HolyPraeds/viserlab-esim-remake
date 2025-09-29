<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Copy retail_price to price column
$updated = \App\Models\Plan::where('price', 0)->update([
    'price' => \Illuminate\Support\Facades\DB::raw('retail_price')
]);

echo "Updated $updated plans with correct prices!\n";

// Verify the fix
$plans = \App\Models\Plan::select('id', 'name', 'retail_price', 'price')->limit(5)->get();
echo "\nVerification:\n";
foreach ($plans as $plan) {
    echo "ID: {$plan->id} - {$plan->name}\n";
    echo "Retail: {$plan->retail_price} | Price: {$plan->price}\n";
    echo "Match: " . ($plan->retail_price == $plan->price ? 'YES' : 'NO') . "\n---\n";
}



