<?php
/**
 * Simple API Test Script
 * Run this after setting up the project to test if API is working
 */

$baseUrl = 'http://localhost'; // Change this to your local URL

echo "🔍 Testing ViserLab eSIM API\n";
echo "=============================\n\n";

$endpoints = [
    '/api/countries' => 'Get all countries',
    '/api/regions' => 'Get all regions',
    '/api/plans' => 'Get all plans',
    '/api/countries/united-states/plans' => 'Get plans for USA',
    '/api/search/countries?q=usa' => 'Search countries'
];

foreach ($endpoints as $endpoint => $description) {
    echo "Testing: {$description}\n";
    echo "URL: {$baseUrl}{$endpoint}\n";
    
    $url = $baseUrl . $endpoint;
    $response = file_get_contents($url);
    
    if ($response === false) {
        echo "❌ Error: Could not connect to API\n";
        echo "   Make sure your web server is running\n";
    } else {
        $data = json_decode($response, true);
        if ($data && isset($data['status'])) {
            if ($data['status'] === 'success') {
                echo "✅ Success: API is working\n";
                if (isset($data['data']) && is_array($data['data'])) {
                    echo "   Found " . count($data['data']) . " items\n";
                }
            } else {
                echo "⚠️  Warning: API returned error\n";
                echo "   Message: " . ($data['message'] ?? 'Unknown error') . "\n";
            }
        } else {
            echo "❌ Error: Invalid JSON response\n";
            echo "   Response: " . substr($response, 0, 100) . "...\n";
        }
    }
    
    echo "\n";
}

echo "🎯 API Test Complete!\n";
echo "If you see errors, check:\n";
echo "1. Web server is running\n";
echo "2. Database is connected\n";
echo "3. Test data is seeded\n";
echo "4. .env file is configured\n";
