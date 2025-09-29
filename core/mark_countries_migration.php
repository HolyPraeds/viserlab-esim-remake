<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Mark countries migration as completed since table already exists
\Illuminate\Support\Facades\DB::table('migrations')->insert([
    'migration' => '2025_08_24_205434_create_countries_table',
    'batch' => 7
]);

echo "Countries migration marked as completed!\n";



