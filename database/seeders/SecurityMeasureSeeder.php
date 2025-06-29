<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SecurityMeasure;

class SecurityMeasureSeeder extends Seeder
{
    public function run(): void
    {
        SecurityMeasure::factory(5)->create();
    }
}
