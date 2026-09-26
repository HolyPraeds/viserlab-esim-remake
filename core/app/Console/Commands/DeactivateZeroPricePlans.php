<?php

namespace App\Console\Commands;

use App\Models\Plan;
use Illuminate\Console\Command;

class DeactivateZeroPricePlans extends Command
{
    protected $signature = 'plans:deactivate-zero-price';
    protected $description = 'Set status=0 for plans whose customer-facing price rounds to 0';

    public function handle()
    {
        $ids = Plan::where('status', 1)
            ->get(['id', 'retail_price', 'price'])
            ->filter(fn (Plan $plan) => planCustomerPrice($plan) < 0.01)
            ->pluck('id');

        $count = 0;
        if ($ids->isNotEmpty()) {
            $count = Plan::whereIn('id', $ids)->update(['status' => 0]);
        }

        $this->info("Deactivated {$count} plan(s) with customer price <= 0.");
        return 0;
    }
}
