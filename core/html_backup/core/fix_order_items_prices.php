<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Update all order items with correct prices from plans
$orderItems = \App\Models\OrderItem::with('plan')->get();

$updated = 0;
foreach ($orderItems as $item) {
    if ($item->plan && $item->price != $item->plan->price) {
        $oldPrice = $item->price;
        $item->price = $item->plan->price;
        $item->save();
        $updated++;
        
        echo "Updated OrderItem ID {$item->id}: {$oldPrice} -> {$item->plan->price} (Plan: {$item->plan->name})\n";
    }
}

echo "\nUpdated $updated order items with correct prices!\n";



