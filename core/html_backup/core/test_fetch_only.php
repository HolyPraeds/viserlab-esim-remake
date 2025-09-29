<?php
// Тест только fetchPlans без addOrUpdatePlans
echo "Testing fetchPlans only...\n";

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
        echo "Successfully fetched " . count($data['obj']['packageList']) . " plans\n";
        echo "First plan slug: " . $data['obj']['packageList'][0]['slug'] . "\n";
        echo "First plan locationCode: " . $data['obj']['packageList'][0]['locationCode'] . "\n";
    }
}




