<?php

/**
 * Скрипт для проверки работы API курсов валют
 * Запуск: php check_currency_api.php
 */

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Currency;

echo "=== Проверка API курсов валют ===\n\n";

// 1. Проверяем наличие валют в БД
echo "1. Проверка валют в базе данных:\n";
$baseCurrency = gs('cur_text');
echo "   Базовая валюта: {$baseCurrency}\n\n";

$currencies = Currency::where('api_currency', '!=', $baseCurrency)
    ->whereNotNull('conversion_rate')
    ->get();

if ($currencies->isEmpty()) {
    echo "   ❌ Валюты не найдены в таблице currencies!\n";
    echo "   Нужно добавить записи для EUR и GBP.\n\n";
} else {
    echo "   ✅ Найдено валют: " . $currencies->count() . "\n";
    foreach ($currencies as $currency) {
        echo "   - {$currency->api_currency}: курс = {$currency->conversion_rate}\n";
    }
    echo "\n";
}

// 2. Проверяем маршрут
echo "2. Проверка маршрута:\n";
try {
    $route = route('api.currency.rates');
    echo "   ✅ Маршрут определён: {$route}\n\n";
} catch (\Exception $e) {
    echo "   ❌ Ошибка маршрута: " . $e->getMessage() . "\n";
    echo "   Проверьте, что routes/api.php обновлён и выполнен 'php artisan route:clear'\n\n";
}

// 3. Проверяем метод в контроллере
echo "3. Проверка контроллера:\n";
if (method_exists(\App\Http\Controllers\ApiController::class, 'currencyRates')) {
    echo "   ✅ Метод currencyRates() существует в ApiController\n\n";
} else {
    echo "   ❌ Метод currencyRates() не найден в ApiController\n\n";
}

// 4. Тестируем API endpoint
echo "4. Тест API endpoint:\n";
try {
    $controller = new \App\Http\Controllers\ApiController();
    $response = $controller->currencyRates();
    $data = json_decode($response->getContent(), true);
    
    if ($data && $data['status'] === 'success') {
        echo "   ✅ API работает корректно\n";
        echo "   Базовая валюта: {$data['base_currency']}\n";
        echo "   Курсы:\n";
        foreach ($data['rates'] as $code => $rateData) {
            echo "     - {$code}: {$rateData['rate']}\n";
        }
    } else {
        echo "   ❌ API вернул ошибку: " . ($data['message'] ?? 'Unknown error') . "\n";
    }
} catch (\Exception $e) {
    echo "   ❌ Ошибка при вызове API: " . $e->getMessage() . "\n";
    echo "   Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== Проверка завершена ===\n";







