<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Currency;

echo "Setting current EUR/USD rate...\n\n";

// Устанавливаем актуальный курс: 1 EUR = 1.085 USD (примерно)
$usdCurrency = Currency::where('api_currency', 'USD')->first();

if ($usdCurrency) {
    echo "Current USD rate: " . $usdCurrency->conversion_rate . "\n";
    
    // Устанавливаем актуальный курс
    $usdCurrency->conversion_rate = 1.085; // 1 EUR = 1.085 USD
    $usdCurrency->save();
    
    echo "✅ Updated USD rate to: " . $usdCurrency->conversion_rate . "\n";
    echo "This means: 1 EUR = " . $usdCurrency->conversion_rate . " USD\n";
    echo "For conversion: 1 USD = " . round(1 / $usdCurrency->conversion_rate, 4) . " EUR\n";
    
    // Покажем примеры конвертации
    echo "\n=== Conversion Examples ===\n";
    $examples = [100, 200, 360, 680];
    foreach ($examples as $usdPrice) {
        $eurPrice = round($usdPrice / $usdCurrency->conversion_rate, 2);
        echo "$$usdPrice USD = €$eurPrice EUR\n";
    }
    
} else {
    echo "❌ USD currency not found in database\n";
}

echo "\nDone!\n";

