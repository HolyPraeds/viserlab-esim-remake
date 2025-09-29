<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Mark remaining migrations as completed
$remainingMigrations = [
    '2025_08_24_205847_add_countries_list_to_regions',
    '2025_08_24_215831_add_countries_list_name_to_region'
];

$batch = 9;
foreach ($remainingMigrations as $migration) {
    // Check if migration is already recorded
    $exists = \Illuminate\Support\Facades\DB::table('migrations')
        ->where('migration', $migration)
        ->exists();
        
    if (!$exists) {
        \Illuminate\Support\Facades\DB::table('migrations')->insert([
            'migration' => $migration,
            'batch' => $batch
        ]);
        echo "Marked migration: $migration\n";
    } else {
        echo "Migration already exists: $migration\n";
    }
}

echo "All migrations marked as completed!\n";



