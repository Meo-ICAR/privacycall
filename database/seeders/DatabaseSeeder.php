<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Company;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed default roles
        $roles = ['superadmin', 'admin', 'user', 'manager', 'employee'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Create superadmin user
        $this->call([
            SuperAdminSeeder::class,
        ]);

        // Seed holdings and companies with proper relationships
        $this->call([
            CompanyWithHoldingSeeder::class,
        ]);

        // Create additional test users
        User::firstOrCreate([
            'email' => 'admin@privacycall.com'
        ], [
            'name' => 'Test Admin',
            'password' => bcrypt('Admin@2024!'),
        ])->assignRole('admin');

        User::firstOrCreate([
            'email' => 'manager@privacycall.com'
        ], [
            'name' => 'Test Manager',
            'password' => bcrypt('Manager@2024!'),
        ])->assignRole('manager');

        User::firstOrCreate([
            'email' => 'employee@privacycall.com'
        ], [
            'name' => 'Test Employee',
            'password' => bcrypt('Employee@2024!'),
        ])->assignRole('employee');

        // Seed example permissions
        $permissions = [
            'manage users',
            'manage companies',
            'manage employees',
            'manage customers',
            'manage suppliers',
            'manage gdpr',
        ];
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign all permissions to superadmin
        $superadminRole = Role::where('name', 'superadmin')->first();
        $superadminRole->syncPermissions(Permission::all());

        // Assign company management permissions to admin
        $adminRole = Role::where('name', 'admin')->first();
        $adminRole->syncPermissions([
            'manage users',
            'manage companies',
            'manage employees',
            'manage customers',
            'manage suppliers',
            'manage gdpr',
        ]);

        // Seed demo data for existing companies
        \App\Models\Company::all()->each(function ($company) {
            // Seed users for each company
            $admin = \App\Models\User::factory()->create([
                'company_id' => $company->id,
                'email' => 'admin_' . $company->id . '_' . uniqid() . '@demo.com',
            ]);
            $admin->assignRole('admin');

            $employees = \App\Models\Employee::factory(10)->create(['company_id' => $company->id]);
            $customers = \App\Models\Customer::factory(10)->create(['company_id' => $company->id]);
            $suppliers = \App\Models\Supplier::factory(5)->create(['company_id' => $company->id]);

            // Seed users for employees
            foreach ($employees as $employee) {
                $user = \App\Models\User::factory()->create([
                    'company_id' => $company->id,
                    'email' => 'employee_' . $employee->id . '_' . uniqid() . '@demo.com',
                ]);
                $user->assignRole('user');
            }

            // Seed users for customers
            foreach ($customers as $customer) {
                $user = \App\Models\User::factory()->create([
                    'company_id' => $company->id,
                    'email' => 'customer_' . $customer->id . '_' . uniqid() . '@demo.com',
                ]);
                $user->assignRole('user');
            }

            // Seed data processing activities
            $activities = \App\Models\DataProcessingActivity::factory(3)->create(['company_id' => $company->id]);

            // Seed consent records
            foreach ($customers as $customer) {
                \App\Models\ConsentRecord::factory()->create([
                    'company_id' => $company->id,
                    'customer_id' => $customer->id,
                ]);
            }
        });

        // Seed document types
        $docTypes = ['Startup', 'Current', 'Compilance', 'Ispection'];
        foreach ($docTypes as $type) {
            \App\Models\DocumentType::firstOrCreate(['name' => $type]);
        }

        // Seed employer types
        $employerTypes = ['Startup', 'SME', 'Corporate', 'Nonprofit'];
        foreach ($employerTypes as $type) {
            \App\Models\EmployerType::firstOrCreate(['name' => $type]);
        }

        // Seed customer types
        $customerTypes = ['Individual', 'Business', 'VIP', 'Partner'];
        foreach ($customerTypes as $type) {
            \App\Models\CustomerType::firstOrCreate(['name' => $type]);
        }

        $this->call([
            EmployeeSeeder::class,
            CustomerSeeder::class,
            SupplierSeeder::class,
            MandatorSeeder::class,
            ConsentRecordSeeder::class,
            DataProcessingActivitySeeder::class,
            TrainingSeeder::class,
            GdprTrainingSeeder::class,
            CustomerTypeSeeder::class,
            SupplierTypeSeeder::class,
            EmployerTypeSeeder::class,
            DocumentTypeSeeder::class,
            DocumentSeeder::class,
            UserSeeder::class,
            EmailTemplateSeeder::class,
            SupplierInspectionSeeder::class,
            EmailProviderSeeder::class,
            DataRemovalRequestAuditLogSeeder::class,
            ProcessingRegisterVersionSeeder::class,
            ProcessingRegCSeeder::class,
            AuditRequestSeeder::class,
            EmailLogSeeder::class,
            EmailReplyAttachmentSeeder::class,
            EmailDocumentSeeder::class,
        ]);
    }
}
