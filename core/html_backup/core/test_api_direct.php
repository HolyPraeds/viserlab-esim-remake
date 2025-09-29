<?php
// Простой тест API без Laravel
echo "Testing API directly...\n";

$apiKey = '6fd724115d2946dbac6e603951d7cad4'; // Реальный ключ
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
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response: " . substr($response, 0, 500) . "...\n";

if ($response) {
    $data = json_decode($response, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "JSON parsed successfully\n";
        if (isset($data['obj']['packageList'])) {
            echo "Found " . count($data['obj']['packageList']) . " plans\n";
            
            // Проверим первые 5 планов на слеши
            for ($i = 0; $i < min(5, count($data['obj']['packageList'])); $i++) {
                $plan = $data['obj']['packageList'][$i];
                echo "\nPlan " . ($i + 1) . ":\n";
                echo "  Slug: " . $plan['slug'] . "\n";
                echo "  Name: " . $plan['name'] . "\n";
                echo "  Location: " . $plan['location'] . "\n";
                echo "  LocationCode: " . $plan['locationCode'] . "\n";
                
                // Проверим все поля на слеши
                foreach ($plan as $key => $value) {
                    if (is_string($value) && strpos($value, '\\') !== false) {
                        echo "  WARNING: $key contains backslashes: '$value'\n";
                    }
                }
            }
        }
    } else {
        echo "JSON parse error: " . json_last_error_msg() . "\n";
    }
}
