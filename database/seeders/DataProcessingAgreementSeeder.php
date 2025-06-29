<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DataProcessingAgreement;

class DataProcessingAgreementSeeder extends Seeder
{
    public function run(): void
    {
        DataProcessingAgreement::factory(10)->create();
    }
}
