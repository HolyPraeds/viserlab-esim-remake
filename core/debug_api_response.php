<?php
/**
 * Отладка ответа API
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$apiKey = gs('plan_api');
echo "API Key: " . substr($apiKey, 0, 20) . "...\n\n";

$baseUrl = 'https://api.esimaccess.com/api/v1/open';
$headers = [
    "RT-AccessCode: $apiKey",
    'Content-Type: application/json',
];

$postData = json_encode(['locationCode' => '']);

echo "Запрос к: {$baseUrl}/package/list\n";
echo "Данные: {$postData}\n\n";

try {
    $response = \App\Lib\CurlRequest::curlPostContent(
        $baseUrl . '/package/list',
        $postData,
        $headers
    );
    
    echo "Ответ (raw):\n";
    echo substr($response, 0, 500) . "...\n\n";
    
    $decoded = json_decode($response, true);
    
    echo "Ответ (decoded):\n";
    print_r($decoded);
    
    if (isset($decoded['obj'])) {
        echo "\n✅ Структура 'obj' найдена\n";
        if (isset($decoded['obj']['packageList'])) {
            echo "✅ packageList найден, количество: " . count($decoded['obj']['packageList']) . "\n";
        } else {
            echo "❌ packageList не найден\n";
            echo "Ключи в obj: " . implode(', ', array_keys($decoded['obj'])) . "\n";
        }
    } else {
        echo "\n❌ Структура 'obj' не найдена\n";
        echo "Ключи в ответе: " . implode(', ', array_keys($decoded ?? [])) . "\n";
    }
    
} catch (\Exception $e) {
    echo "Ошибка: " . $e->getMessage() . "\n";
}







