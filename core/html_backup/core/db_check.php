<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "DB: " . config('database.connections.' . config('database.default') . '.database') . PHP_EOL;
echo "Users: " . \App\Models\User::count() . PHP_EOL;
echo "User logins table exists: " . (Schema::hasTable('user_logins') ? 'YES' : 'NO') . PHP_EOL;



