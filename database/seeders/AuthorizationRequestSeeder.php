<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AuthorizationRequest;

class AuthorizationRequestSeeder extends Seeder
{
    public function run(): void
    {
        AuthorizationRequest::factory(10)->create();
    }
}
