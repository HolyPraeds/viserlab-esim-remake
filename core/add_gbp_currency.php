<?php
/**
 * Добавление GBP валюты с курсом 0.87
 */

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Currency;

echo "=== Добавление GBP валюты ===\n\n";

$baseCurrency = gs('cur_text');
echo "Базовая валюта: {$baseCurrency}\n\n";

// Ищем или создаём GBP
$gbpCurrency = Currency::where('api_currency', 'GBP')->first();

if (!$gbpCurrency) {
    echo "Создаём новую валюту GBP...\n";
    $gbpCurrency = new Currency();
    $gbpCurrency->api_currency = 'GBP';
}

$gbpCurrency->conversion_rate = 0.87; // 1 EUR = 0.87 GBP
$gbpCurrency->save();

echo "✅ GBP валюта сохранена:\n";
echo "   Код: {$gbpCurrency->api_currency}\n";
echo "   Курс: {$gbpCurrency->conversion_rate}\n";
echo "   Это значит: 1 {$baseCurrency} = {$gbpCurrency->conversion_rate} GBP\n\n";

// Проверяем все валюты
echo "Все валюты в БД:\n";
$allCurrencies = Currency::whereNotNull('conversion_rate')->get();
foreach ($allCurrencies as $curr) {
    echo "   - {$curr->api_currency}: {$curr->conversion_rate}\n";
}

echo "\n✅ Готово!\n";







