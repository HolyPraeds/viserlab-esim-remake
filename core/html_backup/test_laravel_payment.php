<?php
/**
 * Test Laravel Payment Integration
 */

require_once 'core/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'core/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\TaurixyService;
use App\Models\Plan;
use App\Models\Payment;
use Illuminate\Support\Str;

echo "🔍 Testing Laravel Payment Integration\n";
echo "=====================================\n\n";

$taurixyService = new TaurixyService();

// Получаем первый план для тестирования
$plan = Plan::where('status', 1)->first();

if (!$plan) {
    echo "❌ No active plans found in database\n";
    exit(1);
}

echo "✅ Found plan: {$plan->name}\n";
echo "   Price: {$plan->retail_price} {$plan->price_currency}\n\n";

// Тестируем создание платежа через Laravel
echo "1. Testing payment creation through Laravel...\n";

$paymentData = [
    'amount' => $plan->retail_price,
    'currency' => $plan->price_currency ?? 'USD',
    'reference_id' => 'LARAVEL-TEST-' . time(),
    'description' => 'Laravel Test eSIM Purchase: ' . $plan->name,
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
    
    // Сохраняем платеж в базу данных
    $payment = Payment::create([
        'user_id' => null,
        'plan_id' => $plan->id,
        'amount' => $plan->retail_price,
        'currency' => $plan->price_currency ?? 'USD',
        'payment_type' => 'DEPOSIT',
        'payment_method' => 'BASIC_CARD',
        'reference_id' => $paymentData['reference_id'],
        'taurixy_payment_id' => $response['result']['id'] ?? null,
        'state' => $response['result']['state'] ?? 'PENDING',
        'redirect_url' => $response['result']['redirectUrl'] ?? null,
        'description' => $paymentData['description'],
        'customer_data' => $paymentData['customer'],
        'webhook_data' => $response['result'] ?? null,
    ]);
    
    echo "✅ Payment saved to database with ID: {$payment->id}\n";
    
    // Сохраняем ID платежа для дальнейшего тестирования
    $paymentId = $response['result']['id'] ?? null;
    
    if ($paymentId) {
        echo "\n2. Testing payment status retrieval...\n";
        $statusResponse = $taurixyService->getPaymentStatus($paymentId);
        
        if (isset($statusResponse['error'])) {
            echo "❌ Failed to get payment status: " . $statusResponse['error'] . "\n";
        } else {
            echo "✅ Payment status retrieved successfully!\n";
            echo "   State: " . ($statusResponse['result']['state'] ?? 'N/A') . "\n";
            echo "   Amount: " . ($statusResponse['result']['amount'] ?? 'N/A') . "\n";
            echo "   Currency: " . ($statusResponse['result']['currency'] ?? 'N/A') . "\n";
            
            // Обновляем статус в базе данных
            $payment->update([
                'state' => $statusResponse['result']['state'] ?? $payment->state,
                'webhook_data' => $statusResponse['result'] ?? null,
            ]);
            echo "✅ Payment status updated in database\n";
        }
    }
}

echo "\n3. Testing API endpoints...\n";

// Тестируем API endpoint для создания платежа
echo "   Testing /api/payments/create endpoint...\n";
$apiUrl = 'http://localhost/api/payments/create';
$apiData = [
    'plan_id' => $plan->id,
    'payment_method' => 'BASIC_CARD',
    'customer_email' => 'test@example.com',
    'customer_first_name' => 'John',
    'customer_last_name' => 'Doe',
    'customer_phone' => '123 4567890',
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($apiData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$apiResponse = curl_exec($ch);
$apiHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "   HTTP Code: {$apiHttpCode}\n";
echo "   Response: {$apiResponse}\n";

echo "\n🎯 Laravel payment integration test completed!\n";
echo "\nNext steps:\n";
echo "1. Check the redirect URL in browser\n";
echo "2. Test webhook processing\n";
echo "3. Verify payment status updates\n";
echo "4. Test the frontend integration\n";
