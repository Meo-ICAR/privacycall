<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DisclosureType;

class DisclosureTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $disclosureTypes = [
            // GDPR Compliance
            [
                'name' => 'gdpr_updates',
                'display_name' => 'GDPR Updates',
                'description' => 'Important updates and changes to GDPR regulations and compliance requirements',
                'category' => 'compliance',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'data_breach_notifications',
                'display_name' => 'Data Breach Notifications',
                'description' => 'Notifications about data breaches and security incidents',
                'category' => 'security',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'privacy_policy_changes',
                'display_name' => 'Privacy Policy Changes',
                'description' => 'Updates to privacy policies and data handling practices',
                'category' => 'privacy',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'consent_management_updates',
                'display_name' => 'Consent Management Updates',
                'description' => 'Changes to consent collection and management processes',
                'category' => 'compliance',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'data_processing_activities',
                'display_name' => 'Data Processing Activities',
                'description' => 'Updates about data processing activities and purposes',
                'category' => 'privacy',
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'data_subject_rights',
                'display_name' => 'Data Subject Rights',
                'description' => 'Information about data subject rights and how to exercise them',
                'category' => 'compliance',
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'name' => 'international_transfers',
                'display_name' => 'International Data Transfers',
                'description' => 'Updates about international data transfers and adequacy decisions',
                'category' => 'compliance',
                'is_active' => true,
                'sort_order' => 7,
            ],
            [
                'name' => 'security_measures',
                'display_name' => 'Security Measures',
                'description' => 'Updates about security measures and data protection safeguards',
                'category' => 'security',
                'is_active' => true,
                'sort_order' => 8,
            ],
            [
                'name' => 'regulatory_guidance',
                'display_name' => 'Regulatory Guidance',
                'description' => 'Guidance from data protection authorities and regulatory bodies',
                'category' => 'compliance',
                'is_active' => true,
                'sort_order' => 9,
            ],
            [
                'name' => 'audit_findings',
                'display_name' => 'Audit Findings',
                'description' => 'Results from privacy and security audits',
                'category' => 'compliance',
                'is_active' => true,
                'sort_order' => 10,
            ],
            [
                'name' => 'vendor_assessments',
                'display_name' => 'Vendor Assessments',
                'description' => 'Updates about third-party vendor privacy and security assessments',
                'category' => 'security',
                'is_active' => true,
                'sort_order' => 11,
            ],
            [
                'name' => 'incident_response',
                'display_name' => 'Incident Response',
                'description' => 'Information about incident response procedures and updates',
                'category' => 'security',
                'is_active' => true,
                'sort_order' => 12,
            ],
            [
                'name' => 'training_updates',
                'display_name' => 'Training Updates',
                'description' => 'Updates about privacy and security training programs',
                'category' => 'general',
                'is_active' => true,
                'sort_order' => 13,
            ],
            [
                'name' => 'compliance_deadlines',
                'display_name' => 'Compliance Deadlines',
                'description' => 'Important compliance deadlines and regulatory requirements',
                'category' => 'compliance',
                'is_active' => true,
                'sort_order' => 14,
            ],
            [
                'name' => 'best_practices',
                'display_name' => 'Best Practices',
                'description' => 'Privacy and security best practices and recommendations',
                'category' => 'general',
                'is_active' => true,
                'sort_order' => 15,
            ],
        ];

        foreach ($disclosureTypes as $disclosureType) {
            DisclosureType::create($disclosureType);
        }
    }
}
