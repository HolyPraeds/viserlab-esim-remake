<?php
require_once 'vendor/autoload.php';

// Простой тест DataPlans
echo "Testing DataPlans...\n";

try {
    $dataPlan = new \App\Lib\DataPlans();
    echo "DataPlans instance created successfully\n";
    
    echo "Testing fetchPlans...\n";
    $plans = $dataPlan->fetchPlans();
    
    if (isset($plans['error'])) {
        echo "Error: " . $plans['error'] . "\n";
    } else {
        echo "Success! Got " . count($plans) . " plans\n";
        
        // Проверим первые несколько планов на наличие слешей
        for ($i = 0; $i < min(3, count($plans)); $i++) {
            $plan = $plans[$i];
            echo "Plan " . ($i + 1) . ": " . json_encode($plan) . "\n";
            
            // Проверим все поля на слеши
            foreach ($plan as $key => $value) {
                if (is_string($value) && strpos($value, '\\') !== false) {
                    echo "WARNING: Field '$key' contains backslashes: '$value'\n";
                }
            }
        }
    }
    
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}




