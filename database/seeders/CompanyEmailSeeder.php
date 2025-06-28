<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\CompanyEmail;
use App\Models\User;
use Illuminate\Database\Seeder;

class CompanyEmailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::whereNotNull('data_controller_contact')
            ->where('data_controller_contact', '!=', '')
            ->get();

        if ($companies->isEmpty()) {
            $this->command->warn('No companies with data controller contacts found. Skipping email seeding.');
            return;
        }

        $users = User::all();

        foreach ($companies as $company) {
            // Create 5-15 emails per company
            $emailCount = rand(5, 15);

            $this->command->info("Creating {$emailCount} emails for company: {$company->name}");

            CompanyEmail::factory()
                ->count($emailCount)
                ->create([
                    'company_id' => $company->id,
                    'to_email' => $company->data_controller_contact,
                    'user_id' => $users->random()->id,
                ]);
        }

        $this->command->info('Company emails seeded successfully!');
        $this->command->info('Created ' . CompanyEmail::count() . ' emails');
    }
}
