<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if superadmin already exists
        $existingSuperAdmin = User::role('superadmin')->first();

        if ($existingSuperAdmin) {
            $this->command->info('Superadmin user already exists. Skipping creation.');
            return;
        }

        // Ensure the superadmin role exists
        Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);

        DB::beginTransaction();

        try {
            // Create superadmin user
            $superAdmin = User::create([
                'name' => 'Super Administrator',
                'email' => 'superadmin@privacycall.com',
                'password' => Hash::make('SuperAdmin@2024!'),
                'is_active' => true,

                // GDPR Compliance - Superadmin consents to all processing
                'gdpr_consent_date' => now(),
                'data_processing_consent' => true,
                'marketing_consent' => true,
                'third_party_sharing_consent' => true,
                'data_retention_consent' => true,
                'right_to_be_forgotten_requested' => false,
                'data_portability_requested' => false,
            ]);

            DB::commit();

            $superAdmin->assignRole('superadmin');

            $this->command->info('Superadmin user created successfully!');
            $this->command->info('Email: superadmin@privacycall.com');
            $this->command->info('Password: SuperAdmin@2024!');
            $this->command->warn('Please change the password after first login!');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Error creating superadmin user: ' . $e->getMessage());
        }
    }
}
