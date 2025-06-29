<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subsupplier;

class SubsupplierSeeder extends Seeder
{
    public function run(): void
    {
        Subsupplier::factory(10)->create();
    }
}
