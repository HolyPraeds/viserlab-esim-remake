<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Check recent Taurixy logs
$logFile = storage_path('logs/laravel.log');
if (file_exists($logFile)) {
    $logs = file_get_contents($logFile);
    
    // Find Taurixy payment requests
    $lines = explode("\n", $logs);
    $taurixyLines = [];
    
    foreach ($lines as $line) {
        if (strpos($line, 'Taurixy payment request') !== false || 
            strpos($line, 'Taurixy payment failed') !== false ||
            strpos($line, 'amount') !== false) {
            $taurixyLines[] = $line;
        }
    }
    
    echo "Recent Taurixy logs:\n";
    foreach (array_slice($taurixyLines, -10) as $line) {
        echo $line . "\n";
    }
} else {
    echo "Log file not found!\n";
}



