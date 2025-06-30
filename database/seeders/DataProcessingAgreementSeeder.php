<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DataProcessingAgreement;
use App\Models\Company;

class DataProcessingAgreementSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure at least one company exists
        if (Company::count() === 0) {
            Company::factory()->create();
        }
        DataProcessingAgreement::factory(10)->create();
    }
}
