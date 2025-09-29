<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Check latest orders
$orders = \App\Models\Order::latest()->limit(5)->get();

echo "Latest orders:\n";
foreach ($orders as $order) {
    echo "Order ID: {$order->id}\n";
    echo "Order Number: {$order->order_number}\n";
    echo "Total Amount: {$order->total_amount}\n";
    echo "Status: {$order->status}\n";
    echo "Created: {$order->created_at}\n";
    echo "---\n";
}

// Check if there are multiple orders with same order_number
$duplicates = \App\Models\Order::select('order_number', \Illuminate\Support\Facades\DB::raw('COUNT(*) as count'))
    ->groupBy('order_number')
    ->having('count', '>', 1)
    ->get();

echo "\nDuplicate order numbers:\n";
foreach ($duplicates as $dup) {
    echo "Order Number: {$dup->order_number} - Count: {$dup->count}\n";
}



