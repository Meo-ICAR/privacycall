<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Company;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create superadmin user
        $this->call([
            SuperAdminSeeder::class,
        ]);

        // Create sample companies for testing
        Company::factory()->count(5)->create();

        // Create additional test users
        User::factory()->create([
            'name' => 'Test Admin',
            'email' => 'admin@privacycall.com',
            'role' => 'admin',
            'password' => bcrypt('Admin@2024!'),
        ]);

        User::factory()->create([
            'name' => 'Test Manager',
            'email' => 'manager@privacycall.com',
            'role' => 'manager',
            'password' => bcrypt('Manager@2024!'),
        ]);

        User::factory()->create([
            'name' => 'Test Employee',
            'email' => 'employee@privacycall.com',
            'role' => 'employee',
            'password' => bcrypt('Employee@2024!'),
        ]);
    }
}
