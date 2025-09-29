<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$database = config('database.connections.mysql.database');

echo "Inspecting schema for database: {$database}\n";

try {
    // List tables
    $tables = DB::select('SELECT table_name FROM information_schema.tables WHERE table_schema = ?', [$database]);
    echo "\nTables (".count($tables)."):\n";
    foreach ($tables as $t) {
        echo "- {$t->table_name}\n";
    }

    $targets = ['currencies', 'plans', 'regions', 'countries', 'country_plan'];
    foreach ($targets as $table) {
        echo "\n=== {$table} ===\n";
        // Describe columns
        $columns = DB::select('SELECT COLUMN_NAME as `Field`, COLUMN_TYPE as `Type`, IS_NULLABLE as `Null`, COLUMN_KEY as `Key`, COLUMN_DEFAULT as `Default`, EXTRA as `Extra` FROM information_schema.columns WHERE table_schema = ? AND table_name = ? ORDER BY ORDINAL_POSITION', [$database, $table]);
        if (!$columns) {
            echo "(table not found)\n";
            continue;
        }
        foreach ($columns as $c) {
            echo sprintf("%-24s %-24s %-6s %-6s %-12s %s\n", $c->Field, $c->Type, $c->Null, $c->Key, (string)$c->Default, $c->Extra);
        }

        // Sample rows/count
        $count = DB::table($table)->count();
        echo "Rows: {$count}\n";
        $sample = DB::table($table)->limit(5)->get();
        if ($sample->count()) {
            echo json_encode($sample, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE)."\n";
        }
    }
} catch (Throwable $e) {
    echo "Error: ".$e->getMessage()."\n";
}






