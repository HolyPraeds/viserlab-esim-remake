<?php
/**
 * Test Payment System
 */

require_once 'core/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'core/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\TaurixyService;
use App\Models\Plan;

echo "🔍 Testing Payment System\n";
echo "========================\n\n";

$taurixyService = new TaurixyService();

// Получаем первый план для тестирования
$plan = Plan::where('status', 1)->first();

if (!$plan) {
    echo "❌ No active plans found in database\n";
    exit(1);
}

echo "✅ Found plan: {$plan->name}\n";
echo "   Price: {$plan->retail_price} {$plan->price_currency}\n\n";

// Тестируем создание платежа
echo "1. Testing payment creation...\n";

$paymentData = [
    'amount' => $plan->retail_price,
    'currency' => $plan->price_currency ?? 'USD',
    'reference_id' => 'TEST-' . time(),
    'description' => 'Test eSIM Purchase: ' . $plan->name,
    'payment_method' => 'BASIC_CARD',
    'customer' => [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'test@example.com',
        'phone' => '1234567890',
    ]
];

$response = $taurixyService->createPayment($paymentData);

if (isset($response['error'])) {
    echo "❌ Payment creation failed: " . $response['error'] . "\n";
    if (isset($response['details'])) {
        echo "   Details: " . json_encode($response['details']) . "\n";
    }
} else {
    echo "✅ Payment created successfully!\n";
    echo "   Payment ID: " . ($response['result']['id'] ?? 'N/A') . "\n";
    echo "   State: " . ($response['result']['state'] ?? 'N/A') . "\n";
    echo "   Redirect URL: " . ($response['result']['redirectUrl'] ?? 'N/A') . "\n";
    
    // Сохраняем ID платежа для дальнейшего тестирования
    $paymentId = $response['result']['id'] ?? null;
    
    if ($paymentId) {
        echo "\n2. Testing payment status...\n";
        $statusResponse = $taurixyService->getPaymentStatus($paymentId);
        
        if (isset($statusResponse['error'])) {
            echo "❌ Failed to get payment status: " . $statusResponse['error'] . "\n";
        } else {
            echo "✅ Payment status retrieved successfully!\n";
            echo "   State: " . ($statusResponse['result']['state'] ?? 'N/A') . "\n";
            echo "   Amount: " . ($statusResponse['result']['amount'] ?? 'N/A') . "\n";
            echo "   Currency: " . ($statusResponse['result']['currency'] ?? 'N/A') . "\n";
        }
    }
}

echo "\n🎯 Payment system test completed!\n";
echo "\nNext steps:\n";
echo "1. Check the redirect URL in browser\n";
echo "2. Test webhook processing\n";
echo "3. Verify payment status updates\n";
