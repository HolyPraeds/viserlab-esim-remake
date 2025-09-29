<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Decrypt the order ID from URL
$encryptedId = 'eyJpdiI6ImVrSHpmNnZ6TEZBNnh1VzdieG1rSVE9PSIsInZhbHVlIjoiaFQ1ZnBpaVJPNVZBcnhRTGttLzVKUT09IiwibWFjIjoiYzEzZDJlODIyNWU2MjQxODljNTMxYzA4MTgxNTVhOWRhMTVkYjQ2ZjUwMmY1MWEyNzE5ZTlkM2RiZDNkNWZkMSIsInRhZyI6IiJ9';

try {
    $orderId = decrypt($encryptedId);
    echo "Decrypted Order ID: $orderId\n";
    
    // Get order details
    $order = \App\Models\Order::find($orderId);
    if ($order) {
        echo "Order found:\n";
        echo "ID: {$order->id}\n";
        echo "Order Number: {$order->order_number}\n";
        echo "Total Amount: {$order->total_amount}\n";
        echo "Status: {$order->status}\n";
        echo "Created: {$order->created_at}\n";
        
        // Check order items
        $orderItems = \App\Models\OrderItem::where('order_id', $order->id)->get();
        echo "\nOrder Items:\n";
        foreach ($orderItems as $item) {
            echo "Plan ID: {$item->plan_id}, Price: {$item->price}\n";
        }
    } else {
        echo "Order not found!\n";
    }
} catch (Exception $e) {
    echo "Decryption failed: " . $e->getMessage() . "\n";
}



