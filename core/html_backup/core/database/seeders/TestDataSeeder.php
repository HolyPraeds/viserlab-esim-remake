<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Models\Region;
use App\Models\Plan;
use App\Models\Currency;
use App\Constants\Status;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default currency
        $currency = Currency::firstOrCreate(
            ['currency_code' => 'USD'],
            [
                'currency_name' => 'US Dollar',
                'currency_symbol' => '$',
                'conversion_rate' => 1.0,
                'status' => Status::ENABLE
            ]
        );

        // Create regions
        $europeRegion = Region::firstOrCreate(
            ['slug' => 'europe'],
            [
                'name' => 'Europe',
                'description' => 'European countries',
                'status' => Status::ENABLE
            ]
        );

        $americaRegion = Region::firstOrCreate(
            ['slug' => 'america'],
            [
                'name' => 'America',
                'description' => 'American countries',
                'status' => Status::ENABLE
            ]
        );

        $asiaRegion = Region::firstOrCreate(
            ['slug' => 'asia'],
            [
                'name' => 'Asia',
                'description' => 'Asian countries',
                'status' => Status::ENABLE
            ]
        );

        // Create countries
        $countries = [
            [
                'name' => 'United States',
                'code' => 'US',
                'slug' => 'united-states',
                'status' => Status::ENABLE,
                'region_id' => $americaRegion->id
            ],
            [
                'name' => 'United Kingdom',
                'code' => 'UK',
                'slug' => 'united-kingdom',
                'status' => Status::ENABLE,
                'region_id' => $europeRegion->id
            ],
            [
                'name' => 'Germany',
                'code' => 'DE',
                'slug' => 'germany',
                'status' => Status::ENABLE,
                'region_id' => $europeRegion->id
            ],
            [
                'name' => 'France',
                'code' => 'FR',
                'slug' => 'france',
                'status' => Status::ENABLE,
                'region_id' => $europeRegion->id
            ],
            [
                'name' => 'Japan',
                'code' => 'JP',
                'slug' => 'japan',
                'status' => Status::ENABLE,
                'region_id' => $asiaRegion->id
            ],
            [
                'name' => 'Canada',
                'code' => 'CA',
                'slug' => 'canada',
                'status' => Status::ENABLE,
                'region_id' => $americaRegion->id
            ]
        ];

        foreach ($countries as $countryData) {
            $country = Country::firstOrCreate(
                ['slug' => $countryData['slug']],
                $countryData
            );
        }

        // Create plans
        $plans = [
            [
                'name' => 'USA 5GB 30 Days',
                'capacity' => 5,
                'capacity_unit' => 'GB',
                'period' => 30,
                'retail_price' => 9.99,
                'price_currency' => 'USD',
                'operator_name' => 'AT&T',
                'operator_slug' => 'att',
                'description' => '5GB data for 30 days in USA',
                'features' => json_encode(['Unlimited calls', 'Unlimited SMS', '5G support']),
                'status' => Status::ENABLE,
                'currency_id' => $currency->id,
                'region_id' => $americaRegion->id
            ],
            [
                'name' => 'USA 10GB 30 Days',
                'capacity' => 10,
                'capacity_unit' => 'GB',
                'period' => 30,
                'retail_price' => 14.99,
                'price_currency' => 'USD',
                'operator_name' => 'Verizon',
                'operator_slug' => 'verizon',
                'description' => '10GB data for 30 days in USA',
                'features' => json_encode(['Unlimited calls', 'Unlimited SMS', '5G support', 'Hotspot']),
                'status' => Status::ENABLE,
                'currency_id' => $currency->id,
                'region_id' => $americaRegion->id
            ],
            [
                'name' => 'UK 3GB 30 Days',
                'capacity' => 3,
                'capacity_unit' => 'GB',
                'period' => 30,
                'retail_price' => 8.99,
                'price_currency' => 'USD',
                'operator_name' => 'EE',
                'operator_slug' => 'ee',
                'description' => '3GB data for 30 days in UK',
                'features' => json_encode(['Unlimited calls', 'Unlimited SMS', '4G support']),
                'status' => Status::ENABLE,
                'currency_id' => $currency->id,
                'region_id' => $europeRegion->id
            ],
            [
                'name' => 'Germany 5GB 30 Days',
                'capacity' => 5,
                'capacity_unit' => 'GB',
                'period' => 30,
                'retail_price' => 12.99,
                'price_currency' => 'USD',
                'operator_name' => 'T-Mobile',
                'operator_slug' => 'tmobile',
                'description' => '5GB data for 30 days in Germany',
                'features' => json_encode(['Unlimited calls', 'Unlimited SMS', '5G support']),
                'status' => Status::ENABLE,
                'currency_id' => $currency->id,
                'region_id' => $europeRegion->id
            ],
            [
                'name' => 'France 7GB 30 Days',
                'capacity' => 7,
                'capacity_unit' => 'GB',
                'period' => 30,
                'retail_price' => 11.99,
                'price_currency' => 'USD',
                'operator_name' => 'Orange',
                'operator_slug' => 'orange',
                'description' => '7GB data for 30 days in France',
                'features' => json_encode(['Unlimited calls', 'Unlimited SMS', '4G support']),
                'status' => Status::ENABLE,
                'currency_id' => $currency->id,
                'region_id' => $europeRegion->id
            ],
            [
                'name' => 'Japan 8GB 30 Days',
                'capacity' => 8,
                'capacity_unit' => 'GB',
                'period' => 30,
                'retail_price' => 19.99,
                'price_currency' => 'USD',
                'operator_name' => 'NTT Docomo',
                'operator_slug' => 'docomo',
                'description' => '8GB data for 30 days in Japan',
                'features' => json_encode(['Unlimited calls', 'Unlimited SMS', '5G support', 'High speed']),
                'status' => Status::ENABLE,
                'currency_id' => $currency->id,
                'region_id' => $asiaRegion->id
            ],
            [
                'name' => 'Canada 6GB 30 Days',
                'capacity' => 6,
                'capacity_unit' => 'GB',
                'period' => 30,
                'retail_price' => 16.99,
                'price_currency' => 'USD',
                'operator_name' => 'Rogers',
                'operator_slug' => 'rogers',
                'description' => '6GB data for 30 days in Canada',
                'features' => json_encode(['Unlimited calls', 'Unlimited SMS', '4G support']),
                'status' => Status::ENABLE,
                'currency_id' => $currency->id,
                'region_id' => $americaRegion->id
            ]
        ];

        foreach ($plans as $planData) {
            $plan = Plan::firstOrCreate(
                ['name' => $planData['name']],
                $planData
            );

            // Attach countries to plans based on region
            if ($plan->region_id == $americaRegion->id) {
                $plan->countries()->syncWithoutDetaching([
                    Country::where('slug', 'united-states')->first()->id,
                    Country::where('slug', 'canada')->first()->id
                ]);
            } elseif ($plan->region_id == $europeRegion->id) {
                $plan->countries()->syncWithoutDetaching([
                    Country::where('slug', 'united-kingdom')->first()->id,
                    Country::where('slug', 'germany')->first()->id,
                    Country::where('slug', 'france')->first()->id
                ]);
            } elseif ($plan->region_id == $asiaRegion->id) {
                $plan->countries()->syncWithoutDetaching([
                    Country::where('slug', 'japan')->first()->id
                ]);
            }
        }

        $this->command->info('Test data seeded successfully!');
        $this->command->info('Created:');
        $this->command->info('- 1 Currency (USD)');
        $this->command->info('- 3 Regions (Europe, America, Asia)');
        $this->command->info('- 6 Countries');
        $this->command->info('- 7 Plans');
    }
}
