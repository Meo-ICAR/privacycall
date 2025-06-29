<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Mandator;
use App\Models\Company;

class MandatorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first company or create one if none exists
        $company = Company::first();

        if (!$company) {
            $company = Company::create([
                'name' => 'Sample Company Ltd',
                'legal_name' => 'Sample Company Limited',
                'email' => 'info@samplecompany.com',
                'phone' => '+1234567890',
                'address_line_1' => '123 Business Street',
                'city' => 'Business City',
                'country' => 'United States',
                'is_active' => true,
            ]);
        }

        $mandators = [
            [
                'company_id' => $company->id,
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john.doe_' . uniqid() . '@samplecompany.com',
                'phone' => '+1234567891',
                'position' => 'Data Protection Officer',
                'department' => 'Legal',
                'disclosure_subscriptions' => ['gdpr_updates', 'data_breach_notifications', 'privacy_policy_changes'],
                'is_active' => true,
                'email_notifications' => true,
                'sms_notifications' => false,
                'preferred_contact_method' => 'email',
                'notes' => 'Primary contact for GDPR compliance matters',
            ],
            [
                'company_id' => $company->id,
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'email' => 'jane.smith@samplecompany.com',
                'phone' => '+1234567892',
                'position' => 'Privacy Manager',
                'department' => 'Compliance',
                'disclosure_subscriptions' => ['gdpr_updates', 'consent_management'],
                'is_active' => true,
                'email_notifications' => true,
                'sms_notifications' => true,
                'preferred_contact_method' => 'email',
                'notes' => 'Handles consent management and privacy policies',
            ],
            [
                'company_id' => $company->id,
                'first_name' => 'Mike',
                'last_name' => 'Johnson',
                'email' => 'mike.johnson@samplecompany.com',
                'phone' => '+1234567893',
                'position' => 'IT Security Manager',
                'department' => 'IT',
                'disclosure_subscriptions' => ['data_breach_notifications', 'security_updates'],
                'is_active' => true,
                'email_notifications' => true,
                'sms_notifications' => true,
                'preferred_contact_method' => 'sms',
                'notes' => 'Responsible for data security and breach response',
            ],
            [
                'company_id' => $company->id,
                'first_name' => 'Sarah',
                'last_name' => 'Wilson',
                'email' => 'sarah.wilson@samplecompany.com',
                'phone' => '+1234567894',
                'position' => 'HR Manager',
                'department' => 'Human Resources',
                'disclosure_subscriptions' => ['employee_data_processing', 'gdpr_updates'],
                'is_active' => false,
                'email_notifications' => false,
                'sms_notifications' => false,
                'preferred_contact_method' => 'email',
                'notes' => 'Former employee - account inactive',
            ],
        ];

        foreach ($mandators as $mandatorData) {
            Mandator::create($mandatorData);
        }

        $this->command->info('Mandators seeded successfully!');
    }
}
