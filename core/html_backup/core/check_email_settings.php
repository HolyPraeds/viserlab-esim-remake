<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Check email verification settings
echo "Email verification setting (ev): " . gs('ev') . PHP_EOL;
echo "SMS verification setting (sv): " . gs('sv') . PHP_EOL;
echo "KYC verification setting (kv): " . gs('kv') . PHP_EOL;

// Check if user needs email verification
$user = \App\Models\User::latest()->first();
if ($user) {
    echo "Latest user email verification status: " . ($user->ev ? 'VERIFIED' : 'UNVERIFIED') . PHP_EOL;
}



