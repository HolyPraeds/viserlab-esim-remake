<?php
/**
 * Load Countries from API
 */

require_once 'core/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'core/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Country;
use App\Lib\DataPlans;
use Illuminate\Support\Str;

echo "🔍 Loading Countries from API\n";
echo "============================\n\n";

$dataPlans = new DataPlans();

// Fetch countries from API
echo "Fetching countries from API...\n";
$countries = $dataPlans->fetchCountries();

if (isset($countries['error'])) {
    echo "❌ Error: " . $countries['error'] . "\n";
    exit(1);
}

echo "✅ Found " . count($countries) . " countries\n\n";

// Process countries
$data = [];
$newCount = 0;
$existingCount = 0;

foreach ($countries as $item) {
    $countryExist = Country::where('code', $item['code'])->exists();
    
    if (!$countryExist) {
        $country = [];
        $country['name'] = $item['name'];
        $country['slug'] = Str::slug($item['name']);
        $country['code'] = $item['code'];
        $country['image'] = 'https://p.qrsim.net/img/flags/' . strtolower($item['code']) . '.png';
        $country['status'] = 1;
        $country['created_at'] = now();
        $country['updated_at'] = now();
        $data[] = $country;
        $newCount++;
    } else {
        $existingCount++;
    }
}

if (empty($data)) {
    echo "ℹ️  No new countries to add. All countries already exist.\n";
} else {
    Country::insert($data);
    echo "✅ Successfully added {$newCount} new countries\n";
}

echo "📊 Summary:\n";
echo "   - New countries: {$newCount}\n";
echo "   - Existing countries: {$existingCount}\n";
echo "   - Total countries in API: " . count($countries) . "\n";

// Check database
$dbCount = Country::count();
echo "   - Total countries in database: {$dbCount}\n";

echo "\n🎯 Countries loaded successfully!\n";
