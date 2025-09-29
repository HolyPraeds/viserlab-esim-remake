<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Get latest order
$order = \App\Models\Order::latest()->first();

if ($order) {
    echo "Order total_amount: {$order->total_amount}\n";
    echo "Order total_amount type: " . gettype($order->total_amount) . "\n";
    
    // Test showAmount function
    echo "showAmount result: " . showAmount($order->total_amount) . "\n";
    echo "gs('cur_text'): " . gs('cur_text') . "\n";
    echo "gs('cur_sym'): " . gs('cur_sym') . "\n";
    
    // Test direct formatting
    echo "number_format: " . number_format($order->total_amount, 2) . "\n";
} else {
    echo "No orders found!\n";
}



