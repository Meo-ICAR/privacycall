<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DataSubjectRightsRequest;

class DataSubjectRightsRequestSeeder extends Seeder
{
    public function run(): void
    {
        DataSubjectRightsRequest::factory(10)->create();
    }
}
