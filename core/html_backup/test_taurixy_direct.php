<?php
/**
 * Direct Taurixy API Test
 */

echo "🔍 Direct Taurixy API Test\n";
echo "==========================\n\n";

// Конфигурация Taurixy
$apiKey = 'xZXxn6LXpasDbTcdUN6u1TsDKzHR27Ky';
$baseUrl = 'https://engine-sandbox.taurixy.com';

// Функция форматирования телефона
function formatPhone($phone) {
    if (!$phone) {
        return '123 4567890';
    }
    
    // Убираем все нецифровые символы
    $phone = preg_replace('/[^0-9]/', '', $phone);
    
    // Если номер длинный, добавляем пробел после первых 3 цифр
    if (strlen($phone) >= 3) {
        return substr($phone, 0, 3) . ' ' . substr($phone, 3);
    }
    
    return $phone;
}

// Тестовые данные
$paymentData = [
    'paymentType' => 'DEPOSIT',
    'amount' => 10.00,
    'currency' => 'USD',
    'referenceId' => 'DIRECT-TEST-' . time(),
    'description' => 'Direct Test eSIM Purchase',
    'returnUrl' => 'https://kradbo.com/success',
    'webhookUrl' => 'https://engine-sandbox.taurixy.com/webhooks/xZXxn6LXpasDbTcdUN6u1TsDKzHR27Ky',
    'customer' => [
        'firstName' => 'John',
        'lastName' => 'Doe',
        'email' => 'test@example.com',
        'phone' => formatPhone('1234567890'),
    ]
];

echo "1. Testing Taurixy API connection...\n";
echo "   Phone: " . formatPhone('1234567890') . "\n\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/api/v1/payments');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($paymentData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $apiKey,
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "❌ cURL Error: " . $error . "\n";
} else {
    echo "✅ HTTP Response Code: " . $httpCode . "\n";
    echo "Response: " . $response . "\n";
    
    $data = json_decode($response, true);
    if ($data && isset($data['result'])) {
        echo "\n✅ Payment created successfully!\n";
        echo "   Payment ID: " . ($data['result']['id'] ?? 'N/A') . "\n";
        echo "   State: " . ($data['result']['state'] ?? 'N/A') . "\n";
        echo "   Redirect URL: " . ($data['result']['redirectUrl'] ?? 'N/A') . "\n";
        
        echo "\n🎉 Direct API test completed successfully!\n";
        echo "\n📋 Next steps:\n";
        echo "1. Go to the website: http://localhost/viserlab-esim-remake\n";
        echo "2. Select a country/plan\n";
        echo "3. Click 'Purchase Now'\n";
        echo "4. Select Taurixy as payment method\n";
        echo "5. Complete the payment process\n";
        
    } else {
        echo "\n❌ Payment creation failed\n";
        echo "   Response: " . $response . "\n";
    }
}

echo "\n🎯 Direct API test completed!\n";
?>
