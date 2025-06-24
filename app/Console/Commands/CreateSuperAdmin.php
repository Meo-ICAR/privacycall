<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Traits\HasRoles;

class CreateSuperAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:superadmin {--email= : Email for the superadmin} {--password= : Password for the superadmin} {--name= : Name for the superadmin}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a superadmin user for the GDPR compliance system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Check if superadmin already exists
        $existingSuperAdmin = User::role('superadmin')->first();

        if ($existingSuperAdmin) {
            $this->warn('A superadmin user already exists!');
            if (!$this->confirm('Do you want to create another superadmin user?')) {
                return;
            }
        }

        // Get user input
        $email = $this->option('email') ?: $this->ask('What is the superadmin email?', 'superadmin@privacycall.com');
        $password = $this->option('password') ?: $this->secret('What is the superadmin password?');
        $name = $this->option('name') ?: $this->ask('What is the superadmin name?', 'Super Administrator');

        // Validate input
        $validator = Validator::make([
            'email' => $email,
            'password' => $password,
            'name' => $name,
        ], [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            $this->error('Validation failed:');
            foreach ($validator->errors()->all() as $error) {
                $this->error('- ' . $error);
            }
            return 1;
        }

        // Confirm creation
        $this->info('Creating superadmin user with the following details:');
        $this->line('Name: ' . $name);
        $this->line('Email: ' . $email);
        $this->line('Password: ' . str_repeat('*', strlen($password)));

        if (!$this->confirm('Do you want to proceed?')) {
            $this->info('Superadmin creation cancelled.');
            return;
        }

        try {
            // Create superadmin user
            $superAdmin = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
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
            $superAdmin->assignRole('superadmin');

            $this->info('✅ Superadmin user created successfully!');
            $this->info('Email: ' . $email);
            $this->warn('Please change the password after first login for security!');

            // Show available commands
            $this->newLine();
            $this->info('Available superadmin commands:');
            $this->line('- php artisan make:superadmin (create another superadmin)');
            $this->line('- php artisan db:seed (run all seeders)');

        } catch (\Exception $e) {
            $this->error('❌ Error creating superadmin user: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
