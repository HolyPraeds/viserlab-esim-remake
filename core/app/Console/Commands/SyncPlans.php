<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncPlans extends Command
{
    protected $signature = 'plans:sync';
    protected $description = 'Fetch plans from API and add/update them';

    public function handle()
    {
        $dataPlan = dataPlans();
        $plans = $dataPlan->fetchPlans();
        if (isset($plans['error'])) {
            $this->error($plans['error']);
            return 1;
        }
        $dataPlan->addOrUpdatePlans($plans);
        $this->call('plans:deactivate-zero-price');
        $this->info('Plans synced successfully.');
        return 0;
    }
}




