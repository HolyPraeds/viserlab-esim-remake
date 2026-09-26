<?php

declare(strict_types=1);

/**
 * Тестовый H2H‑запрос к AlpPay в USD с логами.
 * Отправляет POST /api/v1/payments с картой и amount в долларах.
 *
 * Запуск: php core/scripts/test_alppay_h2h_usd.php
 * Логи: core/storage/logs/alppay_h2h_test.log и stdout
 */

$root = dirname(__DIR__);
require $root . '/vendor/autoload.php';
$app = require_once $root . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

// Лог в файл и в консоль
$logFile = $root . '/storage/logs/alppay_h2h_test.log';
$log = function (string $msg, array $ctx = []) use ($logFile) {
    $line = '[' . date('Y-m-d H:i:s') . '] ' . $msg;
    if (!empty($ctx)) {
        $line .= ' ' . json_encode($ctx, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
    $line .= PHP_EOL;
    file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX);
    echo $line;
};

$log('========== AlpPay H2H test (USD) start ==========');

$baseUrl = rtrim(config('alppay.base_url', ''), '/');
$apiKey  = config('alppay.api_key', '');
$webhook = config('alppay.webhook_url', '');
$shopId  = config('alppay.shop_id', '');

$log('Config', [
    'base_url' => $baseUrl,
    'api_key_set' => !empty($apiKey),
    'api_key_preview' => $apiKey ? substr($apiKey, 0, 8) . '...' : '-',
    'webhook_url' => $webhook,
    'shop_id' => $shopId,
]);

if (empty($baseUrl) || empty($apiKey)) {
    $log('ERROR: ALPPAY_BASE_URL and ALPPAY_API_KEY must be set in .env');
    exit(1);
}

// Тестовые данные (USD)
$amount   = 10.00;
$currency = 'USD';
$uniqueRef = 'H2H_TEST_USD_' . time();

// Тестовая карта (уточни у AlpPay актуальные тест‑номера для sandbox)
$cardNumber = '4111111111111111';
$expiryMonth = '12';
$expiryYear  = '2028';
$cvv         = '123';
$cardholder  = 'Test User';
$customerIp  = '8.8.8.8';

$payload = [
    'paymentType'   => 'DEPOSIT',
    'paymentMethod' => 'BASIC_CARD',
    'description'   => 'H2H Test (USD)',
    'amount'        => (float) $amount,
    'currency'      => $currency,
    'referenceId'   => $uniqueRef,
    'webhookUrl'    => $webhook ?: ('https://example.com/webhooks/alppay'),
    'returnUrl'     => 'https://example.com/return',
    'customer'      => [
        'referenceId' => 'test_user_1',
        'email'       => 'test@example.com',
        'firstName'   => 'Test',
        'lastName'    => 'User',
        'phone'       => '1 5551234567',
        'ip'          => $customerIp,
        'locale'      => 'en',
    ],
    'card' => [
        'cardNumber'       => $cardNumber,
        'cardholderName'   => $cardholder,
        'cardSecurityCode' => $cvv,
        'expiryMonth'      => $expiryMonth,
        'expiryYear'       => $expiryYear,
    ],
    'billingAddress' => [
        'addressLine1' => '123 Test St',
        'city'         => 'New York',
        'postalCode'   => '10001',
        'countryCode'  => 'US',
    ],
];

// В лог — с маскировкой карты
$payloadLog = $payload;
$payloadLog['card'] = [
    'cardNumber'       => substr($cardNumber, 0, 4) . '********' . substr($cardNumber, -4),
    'cardholderName'   => $cardholder,
    'cardSecurityCode' => '***',
    'expiryMonth'      => $expiryMonth,
    'expiryYear'       => $expiryYear,
];

$log('Request payload (card masked)', $payloadLog);

$headers = ['Accept' => 'application/json'];
if ((string) $shopId !== '') {
    $headers['Shop-Id'] = $shopId;
}

$url = $baseUrl . '/api/v1/payments';
$log('POST ' . $url, ['Shop-Id' => $shopId ?: '(not set)']);

$response = Http::withToken($apiKey)
    ->withHeaders($headers)
    ->timeout(30)
    ->post($url, $payload);

$status = $response->status();
$body   = $response->body();
$json   = $response->json();

$log('Response', [
    'status' => $status,
    'body'   => $body,
]);

Log::info('AlpPay H2H test (USD)', [
    'reference_id' => $uniqueRef,
    'amount'       => $amount,
    'currency'     => $currency,
    'status'       => $status,
    'response'     => $json,
]);

if ($response->ok()) {
    $paymentId = $json['result']['id'] ?? $json['id'] ?? null;
    $state     = $json['result']['state'] ?? $json['state'] ?? null;
    $log('OK', ['payment_id' => $paymentId, 'state' => $state]);
} else {
    $log('FAILED', ['status' => $status, 'body' => $body]);
}

$log('========== AlpPay H2H test (USD) end ==========');
$log('Log file: ' . $logFile);
