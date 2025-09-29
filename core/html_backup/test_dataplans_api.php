<?php
/**
 * Test DataPlans API Integration
 * This script tests the external API integration
 */

require_once 'core/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'core/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Lib\DataPlans;

echo "🔍 Testing DataPlans API Integration\n";
echo "=====================================\n\n";

// Check if API key is configured
$apiKey = gs('plan_api');
if (!$apiKey) {
    echo "❌ Error: API key not configured!\n";
    echo "   Please configure the API key in admin panel:\n";
    echo "   Admin Panel -> APIs -> ESIM provider API Key\n\n";
    exit(1);
}

echo "✅ API Key found: " . substr($apiKey, 0, 10) . "...\n\n";

$dataPlans = new DataPlans();

// Test 1: Fetch Countries
echo "1. Testing fetchCountries()...\n";
$countries = $dataPlans->fetchCountries();

if (isset($countries['error'])) {
    echo "❌ Error fetching countries: " . $countries['error'] . "\n";
} else {
    echo "✅ Success: Found " . count($countries) . " countries\n";
    if (count($countries) > 0) {
        echo "   Sample country: " . $countries[0]['name'] . " (" . $countries[0]['code'] . ")\n";
    }
}
echo "\n";

// Test 2: Fetch Regions
echo "2. Testing fetchRegions()...\n";
$regions = $dataPlans->fetchRegions();

if (isset($regions['error'])) {
    echo "❌ Error fetching regions: " . $regions['error'] . "\n";
} else {
    echo "✅ Success: Found " . count($regions) . " regions\n";
    if (count($regions) > 0) {
        echo "   Sample region: " . $regions[0]['name'] . " (" . $regions[0]['code'] . ")\n";
    }
}
echo "\n";

// Test 3: Fetch Plans
echo "3. Testing fetchPlans()...\n";
$plans = $dataPlans->fetchPlans();

if (isset($plans['error'])) {
    echo "❌ Error fetching plans: " . $plans['error'] . "\n";
} else {
    echo "✅ Success: Found " . count($plans) . " plans\n";
    if (count($plans) > 0) {
        echo "   Sample plan: " . $plans[0]['name'] . " (" . $plans[0]['slug'] . ")\n";
        echo "   Price: " . $plans[0]['retailPrice'] . " " . $plans[0]['currencyCode'] . "\n";
    }
}
echo "\n";

// Test 4: Sync Plans to Database
echo "4. Testing syncDataPlan()...\n";
try {
    $dataPlans->addOrUpdatePlans($plans);
    echo "✅ Success: Plans synced to database\n";
    
    // Check database
    $dbPlans = \App\Models\Plan::count();
    echo "   Database now has " . $dbPlans . " plans\n";
} catch (Exception $e) {
    echo "❌ Error syncing plans: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 5: Check Local API
echo "5. Testing Local API endpoints...\n";
$baseUrl = 'http://localhost';

$endpoints = [
    '/api/countries' => 'Get all countries',
    '/api/plans' => 'Get all plans',
    '/api/regions' => 'Get all regions'
];

foreach ($endpoints as $endpoint => $description) {
    echo "   Testing: {$description}\n";
    $url = $baseUrl . $endpoint;
    $response = @file_get_contents($url);
    
    if ($response === false) {
        echo "   ❌ Could not connect to {$url}\n";
        echo "   Make sure your web server is running\n";
    } else {
        $data = json_decode($response, true);
        if ($data && isset($data['status']) && $data['status'] === 'success') {
            $count = isset($data['data']) ? count($data['data']) : 0;
            echo "   ✅ Success: Found {$count} items\n";
        } else {
            echo "   ⚠️  Warning: API returned error\n";
        }
    }
}

echo "\n🎯 Test Complete!\n";
echo "If you see errors:\n";
echo "1. Check API key in admin panel\n";
echo "2. Make sure web server is running\n";
echo "3. Check database connection\n";
echo "4. Run: php artisan config:clear\n";
