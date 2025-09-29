<?php
/**
 * Test Taurixy Integration with Website
 */

require_once 'core/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'core/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Plan;
use App\Models\Gateway;
use App\Models\GatewayCurrency;

echo "🔍 Testing Taurixy Integration with Website\n";
echo "==========================================\n\n";

// Проверяем, что Taurixy добавлен в базу данных
echo "1. Checking Taurixy gateway in database...\n";
$gateway = Gateway::where('code', 'Taurixy')->first();

if ($gateway) {
    echo "✅ Taurixy gateway found!\n";
    echo "   ID: {$gateway->id}\n";
    echo "   Name: {$gateway->name}\n";
    echo "   Status: " . ($gateway->status ? 'Active' : 'Inactive') . "\n";
} else {
    echo "❌ Taurixy gateway not found in database\n";
    exit(1);
}

// Проверяем валюты
echo "\n2. Checking Taurixy currencies...\n";
$currencies = GatewayCurrency::where('method_code', 'Taurixy')->get();

if ($currencies->count() > 0) {
    echo "✅ Found {$currencies->count()} currencies:\n";
    foreach ($currencies as $currency) {
        echo "   - {$currency->currency} ({$currency->symbol})\n";
    }
} else {
    echo "❌ No currencies found for Taurixy\n";
}

// Проверяем планы
echo "\n3. Checking available plans...\n";
$plans = Plan::where('status', 1)->take(3)->get();

if ($plans->count() > 0) {
    echo "✅ Found {$plans->count()} active plans:\n";
    foreach ($plans as $plan) {
        echo "   - {$plan->name} ({$plan->converted_price} {$plan->price_currency})\n";
    }
} else {
    echo "❌ No active plans found\n";
}

// Тестируем создание заказа
echo "\n4. Testing order creation...\n";
$plan = $plans->first();

if ($plan) {
    echo "   Using plan: {$plan->name}\n";
    
    // Создаем тестовый заказ
    $order = new \App\Models\Order();
    $order->user_id = 1; // Тестовый пользователь
    $order->order_number = 'TEST-' . time();
    $order->total_amount = $plan->converted_price;
    $order->status = 0; // Initiated
    $order->save();
    
    echo "✅ Order created successfully!\n";
    echo "   Order ID: {$order->id}\n";
    echo "   Order Number: {$order->order_number}\n";
    echo "   Amount: {$order->total_amount}\n";
    
    // Тестируем создание депозита для Taurixy
    echo "\n5. Testing deposit creation for Taurixy...\n";
    
    $deposit = new \App\Models\Deposit();
    $deposit->user_id = 1;
    $deposit->method_code = 'Taurixy';
    $deposit->method_currency = 'USD';
    $deposit->amount = $order->total_amount;
    $deposit->final_amount = $order->total_amount;
    $deposit->after_charge = $order->total_amount;
    $deposit->charge = 0;
    $deposit->rate = 1;
    $deposit->trx = 'TEST-TRX-' . time();
    $deposit->status = 0; // Pending
    $deposit->save();
    
    echo "✅ Deposit created successfully!\n";
    echo "   Deposit ID: {$deposit->id}\n";
    echo "   Transaction ID: {$deposit->trx}\n";
    echo "   Amount: {$deposit->amount} {$deposit->method_currency}\n";
    
    // Тестируем процесс Taurixy
    echo "\n6. Testing Taurixy payment process...\n";
    
    try {
        $processController = new \App\Http\Controllers\Gateway\Taurixy\ProcessController();
        $result = $processController::process($deposit);
        
        $resultData = json_decode($result, true);
        
        if (isset($resultData['redirect']) && $resultData['redirect']) {
            echo "✅ Taurixy payment created successfully!\n";
            echo "   Redirect URL: {$resultData['redirect_url']}\n";
            echo "\n🎉 Integration test completed successfully!\n";
            echo "\n📋 Next steps:\n";
            echo "1. Go to the website and try to purchase a plan\n";
            echo "2. Select Taurixy as payment method\n";
            echo "3. Complete the payment process\n";
            echo "4. Check webhook processing\n";
        } else {
            echo "❌ Taurixy payment creation failed\n";
            if (isset($resultData['message'])) {
                echo "   Error: {$resultData['message']}\n";
            }
        }
    } catch (Exception $e) {
        echo "❌ Error during Taurixy process: " . $e->getMessage() . "\n";
    }
    
} else {
    echo "❌ No plans available for testing\n";
}

echo "\n🎯 Integration test completed!\n";
?>
