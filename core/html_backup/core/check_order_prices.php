<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Check recent orders and their prices
$orders = \App\Models\Order::with('plan')->latest()->limit(5)->get();

echo "Recent orders:\n";
foreach ($orders as $order) {
    echo "Order ID: {$order->id}\n";
    echo "Amount: {$order->amount}\n";
    echo "Plan: " . ($order->plan ? $order->plan->name : 'No plan') . "\n";
    if ($order->plan) {
        echo "Plan Price: {$order->plan->price}\n";
        echo "Plan Retail Price: {$order->plan->retail_price}\n";
    }
    echo "Status: {$order->status}\n";
    echo "---\n";
}
