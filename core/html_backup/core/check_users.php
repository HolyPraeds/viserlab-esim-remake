<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$users = \App\Models\User::select('id', 'email', 'firstname', 'lastname')->get();
echo "Users in database:\n";
foreach ($users as $user) {
    echo "ID: {$user->id}, Email: '{$user->email}', Name: {$user->firstname} {$user->lastname}\n";
}



