<?php
// Простой тест DataPlans без Laravel
echo "Testing DataPlans logic...\n";

$apiKey = '6fd724115d2946dbac6e603951d7cad4';
$baseUrl = 'https://api.esimaccess.com/api/v1/open';

$headers = [
    "RT-AccessCode: $apiKey",
    'Content-Type: application/json',
];

// Тест fetchPlans
$plansPostData = [
    'locationCode' => ''
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/package/list');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($plansPostData));
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
curl_close($ch);

if ($response) {
    $data = json_decode($response, true);
    if (json_last_error() === JSON_ERROR_NONE && isset($data['obj']['packageList'])) {
        echo "Got " . count($data['obj']['packageList']) . " plans\n";
        
        // Имитируем логику addOrUpdatePlans
        foreach (array_slice($data['obj']['packageList'], 0, 3) as $item) {
            echo "\nProcessing plan: " . $item['slug'] . "\n";
            
            // Очищаем slug
            $cleanSlug = str_replace(['\\', '/', ' '], ['_', '_', '_'], $item['slug']);
            echo "  Clean slug: $cleanSlug\n";
            
            // Проверяем locationCode
            $locationCode = strtoupper($item['locationCode']);
            $isCountryCode = (bool) preg_match('/^[A-Z]{2}$/', $locationCode);
            echo "  LocationCode: $locationCode (is country: " . ($isCountryCode ? 'yes' : 'no') . ")\n";
            
            // Проверяем все поля на слеши
            foreach ($item as $key => $value) {
                if (is_string($value) && strpos($value, '\\') !== false) {
                    echo "  WARNING: $key contains backslashes: '$value'\n";
                }
            }
        }
    }
}




