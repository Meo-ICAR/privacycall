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

        // Seed default roles
        $roles = ['superadmin', 'admin', 'user'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

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
    }
}
