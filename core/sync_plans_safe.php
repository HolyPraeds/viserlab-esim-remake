<?php
/**
 * Безопасная синхронизация планов с обработкой ошибок
 * Запуск: php sync_plans_safe.php
 */

use Illuminate\Contracts\Console\Kernel;

// Ensure CLI-safe server vars
$_SERVER['REQUEST_URI'] = $_SERVER['REQUEST_URI'] ?? '/';
$_SERVER['REQUEST_METHOD'] = $_SERVER['REQUEST_METHOD'] ?? 'GET';

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

echo "=== Синхронизация планов ===\n\n";

// Проверяем API ключ
$apiKey = gs('plan_api');
if (empty($apiKey)) {
    echo "❌ ОШИБКА: API ключ не настроен!\n";
    echo "   Нужно установить plan_api в настройках (General Settings)\n";
    exit(1);
}

echo "API ключ найден: " . substr($apiKey, 0, 10) . "...\n\n";

try {
    $dataPlan = dataPlans();
    
    echo "1. Загрузка планов из API...\n";
    $plans = $dataPlan->fetchPlans();

    if (isset($plans['error'])) {
        echo "   ❌ Ошибка: " . $plans['error'] . "\n";
        exit(1);
    }

    if (empty($plans) || !is_array($plans)) {
        echo "   ❌ Планы не получены или пустой ответ от API\n";
        echo "   Проверьте:\n";
        echo "   - Доступность API esimaccess.com\n";
        echo "   - Правильность API ключа\n";
        exit(1);
    }

    echo "   ✅ Получено планов: " . count($plans) . "\n\n";

    echo "2. Сохранение планов в БД...\n";
    $dataPlan->addOrUpdatePlans($plans);
    
    echo "   ✅ Планы успешно синхронизированы!\n\n";
    
    // Проверяем результат
    $savedPlans = \App\Models\Plan::count();
    echo "3. Итого планов в БД: {$savedPlans}\n";
    
    $activePlans = \App\Models\Plan::where('status', \App\Constants\Status::ENABLE)->count();
    echo "   Активных планов: {$activePlans}\n";

} catch (\Throwable $e) {
    echo "❌ ОШИБКА синхронизации: " . $e->getMessage() . "\n";
    echo "   Файл: " . $e->getFile() . "\n";
    echo "   Строка: " . $e->getLine() . "\n";
    echo "\n   Trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

echo "\n=== Готово! ===\n";







