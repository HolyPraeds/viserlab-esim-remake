<?php
/**
 * Тест валютного функционала локально
 */

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== ТЕСТ ВАЛЮТНОГО ФУНКЦИОНАЛА ===\n\n";

// 1. Проверка базовой валюты
$baseCurrency = gs('cur_text');
echo "1. Базовая валюта: {$baseCurrency}\n\n";

// 2. Проверка валют в БД
echo "2. Валюты в БД:\n";
$currencies = \App\Models\Currency::whereNotNull('conversion_rate')->get();
if ($currencies->isEmpty()) {
    echo "   ❌ Валюты не найдены!\n";
    echo "   Нужно добавить EUR и GBP с курсами.\n\n";
} else {
    foreach ($currencies as $curr) {
        echo "   - {$curr->api_currency}: курс = {$curr->conversion_rate}\n";
    }
    echo "\n";
}

// 3. Проверка API маршрута
echo "3. Проверка API маршрута:\n";
try {
    $route = route('api.currency.rates');
    echo "   ✅ Маршрут: {$route}\n";
    
    // Тестируем контроллер напрямую
    $controller = new \App\Http\Controllers\ApiController();
    $response = $controller->currencyRates();
    $data = json_decode($response->getContent(), true);
    
    if ($data && $data['status'] === 'success') {
        echo "   ✅ API работает!\n";
        echo "   Базовая валюта: {$data['base_currency']}\n";
        echo "   Курсы:\n";
        foreach ($data['rates'] as $code => $rateData) {
            echo "     - {$code}: {$rateData['rate']}\n";
        }
    } else {
        echo "   ❌ API вернул ошибку: " . ($data['message'] ?? 'Unknown') . "\n";
    }
} catch (\Exception $e) {
    echo "   ❌ Ошибка: " . $e->getMessage() . "\n";
}

echo "\n";

// 4. Проверка helper функций
echo "4. Проверка helper функций:\n";
if (function_exists('convertCurrency')) {
    echo "   ✅ convertCurrency() существует\n";
    $testAmount = 100;
    $converted = convertCurrency($testAmount, $baseCurrency, 'EUR');
    echo "   Тест: {$testAmount} {$baseCurrency} = {$converted} EUR\n";
} else {
    echo "   ❌ convertCurrency() не найдена\n";
}

if (function_exists('getCurrencyRate')) {
    echo "   ✅ getCurrencyRate() существует\n";
    $rate = getCurrencyRate('EUR');
    echo "   Курс EUR: " . ($rate ?? 'не найден') . "\n";
} else {
    echo "   ❌ getCurrencyRate() не найдена\n";
}

echo "\n";

// 5. Рекомендации
echo "=== РЕКОМЕНДАЦИИ ===\n\n";

if ($currencies->isEmpty()) {
    echo "1. Добавить валюты:\n";
    echo "   php core/set_current_currency_rate.php\n\n";
}

if (!function_exists('convertCurrency')) {
    echo "2. Проверить helpers.php - функции должны быть загружены\n\n";
}

echo "3. Очистить кэши:\n";
echo "   php artisan route:clear\n";
echo "   php artisan config:clear\n";
echo "   php artisan view:clear\n\n";

echo "4. Проверить в браузере:\n";
echo "   http://localhost/api/currency/rates\n";
echo "   Должен вернуться JSON с курсами\n\n";







