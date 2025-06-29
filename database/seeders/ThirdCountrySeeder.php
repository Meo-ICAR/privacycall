<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ThirdCountry;

class ThirdCountrySeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            ['country_name' => 'United States', 'country_code' => 'US'],
            ['country_name' => 'Canada', 'country_code' => 'CA'],
            ['country_name' => 'Japan', 'country_code' => 'JP'],
            ['country_name' => 'Australia', 'country_code' => 'AU'],
            ['country_name' => 'Brazil', 'country_code' => 'BR'],
        ];
        foreach ($countries as $country) {
            ThirdCountry::updateOrCreate(
                ['country_code' => $country['country_code']],
                [
                    'country_name' => $country['country_name'],
                    'is_active' => true,
                ]
            );
        }
    }
}
