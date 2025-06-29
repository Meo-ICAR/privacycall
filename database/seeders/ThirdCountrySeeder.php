<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ThirdCountry;

class ThirdCountrySeeder extends Seeder
{
    public function run(): void
    {
        ThirdCountry::factory(10)->create();
    }
}
