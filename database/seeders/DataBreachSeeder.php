<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DataBreach;

class DataBreachSeeder extends Seeder
{
    public function run(): void
    {
        DataBreach::factory(10)->create();
    }
}
