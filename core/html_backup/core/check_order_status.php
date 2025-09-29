<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking order status:\n\n";

$latestOrder = \App\Models\Order::latest()->first();

if ($latestOrder) {
    echo "Order ID: {$latestOrder->id}\n";
    echo "Order Number: {$latestOrder->order_number}\n";
    echo "Status: {$latestOrder->status}\n";
    echo "User ID: {$latestOrder->user_id}\n";
    
    // Check if order is initiated
    $isInitiated = $latestOrder->status == 0;
    echo "Is Initiated (status == 0): " . ($isInitiated ? 'YES' : 'NO') . "\n";
    
    // Test the query that OrderController uses
    $order = \App\Models\Order::initiated()->find($latestOrder->id);
    echo "Found by initiated() scope: " . ($order ? 'YES' : 'NO') . "\n";
    
    if ($order) {
        echo "Order found by initiated() scope!\n";
    } else {
        echo "Order NOT found by initiated() scope!\n";
        echo "This means the order status is not 0 or there's an issue with the scope.\n";
    }
    
} else {
    echo "No orders found!\n";
}
?>



