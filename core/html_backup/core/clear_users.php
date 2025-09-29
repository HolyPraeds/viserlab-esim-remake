<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Delete all users
\App\Models\User::truncate();
echo "All users deleted successfully!\n";
echo "Users count: " . \App\Models\User::count() . "\n";



