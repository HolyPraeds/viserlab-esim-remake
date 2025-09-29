<?php
/**
 * Simple Payment Test
 */

echo "🔍 Simple Payment Test\n";
echo "====================\n\n";

// Простой тест API Taurixy
$apiKey = 'xZXxn6LXpasDbTcdUN6u1TsDKzHR27Ky';
$baseUrl = 'https://engine-sandbox.taurixy.com';

$paymentData = [
    'paymentType' => 'DEPOSIT',
    'amount' => 10.00,
    'currency' => 'USD',
    'referenceId' => 'TEST-' . time(),
    'description' => 'Test eSIM Purchase',
    'returnUrl' => 'https://kradbo.com/success',
    'webhookUrl' => 'https://engine-sandbox.taurixy.com/webhooks/xZXxn6LXpasDbTcdUN6u1TsDKzHR27Ky',
    'customer' => [
        'firstName' => 'John',
        'lastName' => 'Doe',
        'email' => 'test@example.com',
        'phone' => '123 4567890',
    ]
];

echo "1. Testing Taurixy API connection...\n";

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
    } else {
        echo "\n❌ Payment creation failed\n";
        echo "   Response: " . $response . "\n";
    }
}

echo "\n🎯 Simple payment test completed!\n";
