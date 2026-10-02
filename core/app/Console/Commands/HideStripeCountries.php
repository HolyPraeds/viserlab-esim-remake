<?php

namespace App\Console\Commands;

use App\Models\Country;
use App\Models\Plan;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class HideStripeCountries extends Command
{
    protected $signature = 'compliance:hide-stripe-countries';
    protected $description = 'Disable destinations and plans Stripe/OFAC treat as high-risk';

    public function handle(): int
    {
        $codes = stripeBlockedCountryCodes();
        $countries = Country::whereIn('code', $codes)->update(['status' => 0]);

        // Only disable country-specific packs. Regional Europe/ME plans that
        // happen to list UA/IQ stay live — the destination itself is hidden.
        $plans = Plan::whereHas('countries', fn ($q) => $q->whereIn('code', $codes))
            ->whereDoesntHave('countries', fn ($q) => $q->whereNotIn('code', $codes))
            ->update(['status' => 0]);
        Cache::forget('active_countries_with_plans');

        $this->info("Disabled {$countries} countr" . ($countries === 1 ? 'y' : 'ies') . " and {$plans} plan(s).");
        $this->line('Codes: ' . implode(', ', $codes));

        return 0;
    }
}
