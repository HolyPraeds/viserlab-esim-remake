<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "APP_URL: " . config('app.url') . "\n";
echo "Base Path: " . base_path() . "\n";
echo "Public Path: " . public_path() . "\n";
echo "Running in console: " . (app()->runningInConsole() ? 'Yes' : 'No') . "\n";
echo "Current working directory: " . getcwd() . "\n";
echo "Document root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Not set') . "\n";




