<?php

declare(strict_types=1);

/**
 * Sync USA/Canada plans from local DB to production DB.
 * Run locally (where .env points to local DB) and provide PROD_DB_* env vars.
 *
 * Required env vars for prod:
 *   PROD_DB_HOST, PROD_DB_PORT, PROD_DB_DATABASE, PROD_DB_USERNAME, PROD_DB_PASSWORD
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

$local = [
    'host' => envValue('DB_HOST', '127.0.0.1'),
    'port' => envValue('DB_PORT', '3306'),
    'database' => envValue('DB_DATABASE'),
    'username' => envValue('DB_USERNAME'),
    'password' => envValue('DB_PASSWORD'),
];

$prod = [
    'host' => envValue('PROD_DB_HOST'),
    'port' => envValue('PROD_DB_PORT', '3306'),
    'database' => envValue('PROD_DB_DATABASE'),
    'username' => envValue('PROD_DB_USERNAME'),
    'password' => envValue('PROD_DB_PASSWORD'),
];

foreach (['database', 'username'] as $req) {
    if (empty($local[$req])) {
        echo "Missing local DB env: DB_{$req}" . PHP_EOL;
        exit(1);
    }
    if (empty($prod[$req])) {
        echo "Missing prod DB env: PROD_DB_" . strtoupper($req) . PHP_EOL;
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

$localPdo = pdoConnect($local);
$prodPdo = pdoConnect($prod);

$targetSlugs = ['united-states', 'canada'];
$rebuildPivot = envValue('REBUILD_PIVOT', '0') === '1';
$deleteZeroPlans = envValue('DELETE_ZERO_PLANS', '1') === '1'; // Удалить нулевые планы перед синком

function tableColumns(PDO $pdo, string $table): array {
    $stmt = $pdo->query("SHOW COLUMNS FROM `{$table}`");
    $cols = [];
    foreach ($stmt->fetchAll() as $row) {
        $cols[] = $row['Field'];
    }
    return $cols;
}

$localCols = tableColumns($localPdo, 'plans');
$prodCols = tableColumns($prodPdo, 'plans');

$commonCols = array_values(array_intersect($localCols, $prodCols));
// Avoid copying auto-increment id
$commonCols = array_values(array_diff($commonCols, ['id']));

if (!in_array('slug', $commonCols, true)) {
    echo "Plans table must have 'slug' column in both DBs." . PHP_EOL;
    exit(1);
}

$placeholders = implode(',', array_fill(0, count($commonCols), '?'));
$colList = '`' . implode('`,`', $commonCols) . '`';
$updateList = implode(',', array_map(fn($c) => "`{$c}`=VALUES(`{$c}`)", $commonCols));

// Get local country IDs
$inPlaceholders = implode(',', array_fill(0, count($targetSlugs), '?'));
$localCountryStmt = $localPdo->prepare("SELECT id, slug FROM countries WHERE slug IN ({$inPlaceholders})");
$localCountryStmt->execute($targetSlugs);
$localCountries = $localCountryStmt->fetchAll();

if (empty($localCountries)) {
    echo "No local countries found for target slugs." . PHP_EOL;
    exit(0);
}

$localCountryIds = array_column($localCountries, 'id');
$localCountryIdsPlaceholders = implode(',', array_fill(0, count($localCountryIds), '?'));

$localPlanIdsStmt = $localPdo->prepare(
    "SELECT DISTINCT plan_id FROM country_plan WHERE country_id IN ({$localCountryIdsPlaceholders})"
);
$localPlanIdsStmt->execute($localCountryIds);
$localPlanIds = array_column($localPlanIdsStmt->fetchAll(), 'plan_id');

if (empty($localPlanIds)) {
    echo "No local plans linked to target countries." . PHP_EOL;
    exit(0);
}

$localPlanIdsPlaceholders = implode(',', array_fill(0, count($localPlanIds), '?'));
$localPlansStmt = $localPdo->prepare(
    "SELECT {$colList} FROM plans WHERE id IN ({$localPlanIdsPlaceholders})"
);
$localPlansStmt->execute($localPlanIds);
$localPlans = $localPlansStmt->fetchAll();

if (empty($localPlans)) {
    echo "No local plan rows fetched." . PHP_EOL;
    exit(0);
}

echo "Found " . count($localPlans) . " plan(s) for USA/Canada in local DB." . PHP_EOL;

// Show plans that will be synced
echo "\nPlans to sync:" . PHP_EOL;
foreach ($localPlans as $plan) {
    $price = $plan['retail_price'] ?? 0;
    $capacity = $plan['capacity'] ?? 0;
    $slug = $plan['slug'] ?? 'N/A';
    echo "  - Slug: {$slug}, Price: {$price}, Capacity: {$capacity}" . PHP_EOL;
}

// Delete zero plans in prod before sync (if enabled)
if ($deleteZeroPlans) {
    echo "\nDeleting zero-price plans in prod..." . PHP_EOL;
    $prodCountryStmt = $prodPdo->prepare("SELECT id FROM countries WHERE slug IN ({$inPlaceholders})");
    $prodCountryStmt->execute($targetSlugs);
    $prodCountryIds = array_column($prodCountryStmt->fetchAll(), 'id');
    
    if (!empty($prodCountryIds)) {
        $prodCountryIdsPlaceholders = implode(',', array_fill(0, count($prodCountryIds), '?'));
        $zeroPlansStmt = $prodPdo->prepare(
            "SELECT p.id, p.slug, p.retail_price, p.capacity 
             FROM plans p 
             JOIN country_plan cp ON cp.plan_id = p.id 
             WHERE cp.country_id IN ({$prodCountryIdsPlaceholders}) 
             AND (p.retail_price = 0 OR p.retail_price IS NULL OR p.capacity = 0 OR p.capacity IS NULL)"
        );
        $zeroPlansStmt->execute($prodCountryIds);
        $zeroPlans = $zeroPlansStmt->fetchAll();
        
        if (!empty($zeroPlans)) {
            echo "  Found " . count($zeroPlans) . " zero-price plan(s) to delete:" . PHP_EOL;
            foreach ($zeroPlans as $zp) {
                echo "    - ID: {$zp['id']}, Slug: {$zp['slug']}, Price: {$zp['retail_price']}, Capacity: {$zp['capacity']}" . PHP_EOL;
            }
            
            $prodPdo->beginTransaction();
            try {
                // Удаляем связи из country_plan
                $zeroPlanIds = array_column($zeroPlans, 'id');
                $zeroPlanIdsPlaceholders = implode(',', array_fill(0, count($zeroPlanIds), '?'));
                $deletePivotStmt = $prodPdo->prepare("DELETE FROM country_plan WHERE plan_id IN ({$zeroPlanIdsPlaceholders})");
                $deletePivotStmt->execute($zeroPlanIds);
                
                // Удаляем сами планы
                $deletePlansStmt = $prodPdo->prepare("DELETE FROM plans WHERE id IN ({$zeroPlanIdsPlaceholders})");
                $deletePlansStmt->execute($zeroPlanIds);
                
                $prodPdo->commit();
                echo "  Deleted " . count($zeroPlans) . " zero-price plan(s) and their links." . PHP_EOL;
            } catch (Throwable $e) {
                $prodPdo->rollBack();
                echo "  ERROR deleting zero plans: " . $e->getMessage() . PHP_EOL;
                throw $e;
            }
        } else {
            echo "  No zero-price plans found to delete." . PHP_EOL;
        }
    }
}

// Upsert plans into prod by slug
$insertSql = "INSERT INTO plans ({$colList}) VALUES ({$placeholders}) ON DUPLICATE KEY UPDATE {$updateList}";
$insertStmt = $prodPdo->prepare($insertSql);

$updated = 0;
$inserted = 0;

foreach ($localPlans as $plan) {
    $values = [];
    foreach ($commonCols as $col) {
        $values[] = $plan[$col];
    }
    
    // Check if plan exists in prod by slug
    $checkStmt = $prodPdo->prepare("SELECT id FROM plans WHERE slug = ?");
    $checkStmt->execute([$plan['slug']]);
    $exists = $checkStmt->fetch();
    
    $insertStmt->execute($values);
    
    if ($exists) {
        $updated++;
    } else {
        $inserted++;
    }
}

echo "\nSync completed:" . PHP_EOL;
echo "  - Updated: {$updated} plan(s)" . PHP_EOL;
echo "  - Inserted: {$inserted} plan(s)" . PHP_EOL;

if ($rebuildPivot) {
    // Map prod country IDs by slug
    $prodCountryStmt = $prodPdo->prepare("SELECT id, slug FROM countries WHERE slug IN ({$inPlaceholders})");
    $prodCountryStmt->execute($targetSlugs);
    $prodCountries = $prodCountryStmt->fetchAll();

    if (count($prodCountries) !== count($targetSlugs)) {
        echo "Prod countries missing for slugs. Found: " . implode(', ', array_column($prodCountries, 'slug')) . PHP_EOL;
        exit(1);
    }

    $prodCountryIds = array_column($prodCountries, 'id');

    // Build plan ID map by slug in prod
    $planSlugs = array_values(array_unique(array_column($localPlans, 'slug')));
    $planSlugPlaceholders = implode(',', array_fill(0, count($planSlugs), '?'));
    $prodPlanStmt = $prodPdo->prepare("SELECT id, slug FROM plans WHERE slug IN ({$planSlugPlaceholders})");
    $prodPlanStmt->execute($planSlugs);
    $prodPlanRows = $prodPlanStmt->fetchAll();
    $prodPlanMap = [];
    foreach ($prodPlanRows as $row) {
        $prodPlanMap[$row['slug']] = (int) $row['id'];
    }

    $missingPlanSlugs = array_diff($planSlugs, array_keys($prodPlanMap));
    if (!empty($missingPlanSlugs)) {
        echo "Missing prod plans for slugs: " . implode(', ', $missingPlanSlugs) . PHP_EOL;
        exit(1);
    }

    // Rebuild pivot for target countries in prod
    $prodPdo->beginTransaction();
    try {
        $prodCountryIdsPlaceholders = implode(',', array_fill(0, count($prodCountryIds), '?'));
        $deleteStmt = $prodPdo->prepare("DELETE FROM country_plan WHERE country_id IN ({$prodCountryIdsPlaceholders})");
        $deleteStmt->execute($prodCountryIds);

        $insertPivot = $prodPdo->prepare("INSERT INTO country_plan (plan_id, country_id) VALUES (?, ?)");
        foreach ($prodCountryIds as $countryId) {
            foreach ($planSlugs as $slug) {
                $insertPivot->execute([$prodPlanMap[$slug], $countryId]);
            }
        }
        $prodPdo->commit();
    } catch (Throwable $e) {
        $prodPdo->rollBack();
        throw $e;
    }

    echo "Synced " . count($localPlans) . " plan(s) and rebuilt pivot for USA/Canada." . PHP_EOL;
} else {
    echo "Synced " . count($localPlans) . " plan(s). Pivot rebuild skipped." . PHP_EOL;
}
