<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestFetchPlans extends Command
{
    protected $signature = 'test:fetch-plans';
    protected $description = 'Test fetchPlans only without addOrUpdatePlans';

    public function handle()
    {
        $this->info('Testing fetchPlans...');
        
        try {
            $dataPlan = dataPlans();
            $this->info('DataPlans instance created successfully');
            
            $plans = $dataPlan->fetchPlans();
            $this->info('fetchPlans called successfully');
            
            if (isset($plans['error'])) {
                $this->error($plans['error']);
                return 1;
            }
            
            $this->info('Successfully fetched ' . count($plans) . ' plans');
            $this->info('First plan slug: ' . $plans[0]['slug']);
            $this->info('First plan locationCode: ' . $plans[0]['locationCode']);
            
            return 0;
            
        } catch (Exception $e) {
            $this->error('Exception: ' . $e->getMessage());
            $this->error('File: ' . $e->getFile() . ':' . $e->getLine());
            return 1;
        }
    }
}




