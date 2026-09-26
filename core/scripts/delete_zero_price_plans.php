<?php

declare(strict_types=1);

/**
 * Удаляет планы с нулевой ценой или нулевой capacity для ВСЕХ стран
 * Запускай на проде или укажи PROD_DB_* переменные
 */

function envValue(string $key, ?string $default = null): ?string {
    $value = getenv($key);
    return $value !== false ? $value : $default;
}

function loadEnv(string $path): void {
    if (!is_file($path)) {
        return;
    }
    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) {
            continue;
        }
        [$k, $v] = explode('=', $line, 2);
        $k = trim($k);
        $v = trim($v);
        $v = trim($v, "\"'");
        if (!getenv($k)) {
            putenv($k . '=' . $v);
        }
    }
}

$root = dirname(__DIR__);
loadEnv($root . '/.env');

// Используем прод БД если указаны переменные, иначе локальную
$db = [
    'host' => envValue('PROD_DB_HOST') ?: envValue('DB_HOST', '127.0.0.1'),
    'port' => envValue('PROD_DB_PORT') ?: envValue('DB_PORT', '3306'),
    'database' => envValue('PROD_DB_DATABASE') ?: envValue('DB_DATABASE'),
    'username' => envValue('PROD_DB_USERNAME') ?: envValue('DB_USERNAME'),
    'password' => envValue('PROD_DB_PASSWORD') ?: envValue('DB_PASSWORD'),
];

foreach (['database', 'username'] as $req) {
    if (empty($db[$req])) {
        echo "Missing DB env: DB_{$req} or PROD_DB_" . strtoupper($req) . PHP_EOL;
        exit(1);
    }
}

function pdoConnect(array $cfg): PDO {
    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
        $cfg['host'],
        $cfg['port'],
        $cfg['database']
    );
    return new PDO($dsn, $cfg['username'], $cfg['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
}

$pdo = pdoConnect($db);

echo "🔍 Поиск нулевых планов (retail_price=0 или capacity=0) для ВСЕХ стран..." . PHP_EOL . PHP_EOL;

// Находим нулевые планы (все страны)
$zeroPlansStmt = $pdo->query(
    "SELECT p.id, p.slug, p.name, p.retail_price, p.capacity, p.price_currency,
            GROUP_CONCAT(c.slug) as countries
     FROM plans p 
     LEFT JOIN country_plan cp ON cp.plan_id = p.id 
     LEFT JOIN countries c ON c.id = cp.country_id
     WHERE (p.retail_price = 0 OR p.retail_price IS NULL OR p.capacity = 0 OR p.capacity IS NULL)
     GROUP BY p.id"
);
$zeroPlans = $zeroPlansStmt->fetchAll();

if (empty($zeroPlans)) {
    echo "✅ Нулевых планов не найдено." . PHP_EOL;
    exit(0);
}

echo "📋 Найдено " . count($zeroPlans) . " нулевых планов:" . PHP_EOL;
foreach ($zeroPlans as $plan) {
    $countries = $plan['countries'] ?? '-';
    echo "   - ID: {$plan['id']}, Slug: {$plan['slug']}, Name: {$plan['name']}, Price: {$plan['retail_price']}, Capacity: {$plan['capacity']}, Countries: {$countries}" . PHP_EOL;
}

echo PHP_EOL . "⚠️  Удаление планов..." . PHP_EOL;

$pdo->beginTransaction();
try {
    $zeroPlanIds = array_column($zeroPlans, 'id');
    $zeroPlanIdsPlaceholders = implode(',', array_fill(0, count($zeroPlanIds), '?'));
    
    // Удаляем связи из country_plan
    $deletePivotStmt = $pdo->prepare("DELETE FROM country_plan WHERE plan_id IN ({$zeroPlanIdsPlaceholders})");
    $deletePivotStmt->execute($zeroPlanIds);
    $pivotDeleted = $deletePivotStmt->rowCount();
    
    // Удаляем сами планы
    $deletePlansStmt = $pdo->prepare("DELETE FROM plans WHERE id IN ({$zeroPlanIdsPlaceholders})");
    $deletePlansStmt->execute($zeroPlanIds);
    $plansDeleted = $deletePlansStmt->rowCount();
    
    $pdo->commit();
    
    echo "✅ Успешно удалено:" . PHP_EOL;
    echo "   - Планов: {$plansDeleted}" . PHP_EOL;
    echo "   - Связей: {$pivotDeleted}" . PHP_EOL;
    
} catch (Throwable $e) {
    $pdo->rollBack();
    echo "❌ ОШИБКА при удалении: " . $e->getMessage() . PHP_EOL;
    exit(1);
}
