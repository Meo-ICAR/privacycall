<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DataCategory;

class DataCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dataCategories = [
            // Personal Data (Low Sensitivity)
            [
                'name' => 'contact_information',
                'description' => 'Basic contact details such as name, email, phone number, and address.',
                'sensitivity_level' => 'low',
                'special_category' => false,
                'gdpr_article_reference' => 'Article 4(1)',
                'processing_requirements' => 'Standard data protection measures required.',
                'retention_requirements' => 'Retain only as long as necessary for the stated purpose.',
                'security_requirements' => 'Basic encryption and access controls.',
                'sort_order' => 1,
            ],
            [
                'name' => 'demographic_data',
                'description' => 'Age, gender, nationality, and other demographic information.',
                'sensitivity_level' => 'low',
                'special_category' => false,
                'gdpr_article_reference' => 'Article 4(1)',
                'processing_requirements' => 'Standard data protection measures required.',
                'retention_requirements' => 'Retain only as long as necessary for the stated purpose.',
                'security_requirements' => 'Basic encryption and access controls.',
                'sort_order' => 2,
            ],
            [
                'name' => 'professional_information',
                'description' => 'Job title, employer, work history, and professional qualifications.',
                'sensitivity_level' => 'low',
                'special_category' => false,
                'gdpr_article_reference' => 'Article 4(1)',
                'processing_requirements' => 'Standard data protection measures required.',
                'retention_requirements' => 'Retain only as long as necessary for the stated purpose.',
                'security_requirements' => 'Basic encryption and access controls.',
                'sort_order' => 3,
            ],

            // Medium Sensitivity Data
            [
                'name' => 'financial_data',
                'description' => 'Bank account details, credit card information, financial transactions, and income data.',
                'sensitivity_level' => 'medium',
                'special_category' => false,
                'gdpr_article_reference' => 'Article 4(1)',
                'processing_requirements' => 'Enhanced security measures and encryption required.',
                'retention_requirements' => 'Strict retention periods, often regulated by financial laws.',
                'security_requirements' => 'Strong encryption, access controls, and audit trails.',
                'sort_order' => 4,
            ],
            [
                'name' => 'location_data',
                'description' => 'GPS coordinates, IP addresses, location history, and travel patterns.',
                'sensitivity_level' => 'medium',
                'special_category' => false,
                'gdpr_article_reference' => 'Article 4(1)',
                'processing_requirements' => 'Enhanced security measures and purpose limitation.',
                'retention_requirements' => 'Limited retention periods, often real-time processing.',
                'security_requirements' => 'Encryption, access controls, and data minimization.',
                'sort_order' => 5,
            ],
            [
                'name' => 'behavioral_data',
                'description' => 'Online behavior, preferences, browsing history, and interaction patterns.',
                'sensitivity_level' => 'medium',
                'special_category' => false,
                'gdpr_article_reference' => 'Article 4(1)',
                'processing_requirements' => 'Enhanced security measures and transparency.',
                'retention_requirements' => 'Limited retention periods with clear purpose.',
                'security_requirements' => 'Encryption, access controls, and user consent.',
                'sort_order' => 6,
            ],

            // High Sensitivity Data
            [
                'name' => 'health_data',
                'description' => 'Medical records, health conditions, treatments, and biometric data.',
                'sensitivity_level' => 'high',
                'special_category' => true,
                'gdpr_article_reference' => 'Article 9(1)',
                'processing_requirements' => 'Explicit consent or specific legal basis required.',
                'retention_requirements' => 'Strict retention periods, often regulated by health laws.',
                'security_requirements' => 'Maximum security measures, encryption, and access controls.',
                'sort_order' => 7,
            ],
            [
                'name' => 'genetic_data',
                'description' => 'DNA sequences, genetic test results, and hereditary information.',
                'sensitivity_level' => 'high',
                'special_category' => true,
                'gdpr_article_reference' => 'Article 9(1)',
                'processing_requirements' => 'Explicit consent or specific legal basis required.',
                'retention_requirements' => 'Permanent or very long-term retention with strict controls.',
                'security_requirements' => 'Maximum security measures and specialized storage.',
                'sort_order' => 8,
            ],
            [
                'name' => 'biometric_data',
                'description' => 'Fingerprints, facial recognition data, voice patterns, and other biometric identifiers.',
                'sensitivity_level' => 'high',
                'special_category' => true,
                'gdpr_article_reference' => 'Article 9(1)',
                'processing_requirements' => 'Explicit consent or specific legal basis required.',
                'retention_requirements' => 'Limited retention periods with strict controls.',
                'security_requirements' => 'Maximum security measures and specialized storage.',
                'sort_order' => 9,
            ],

            // Very High Sensitivity Data
            [
                'name' => 'criminal_data',
                'description' => 'Criminal convictions, offenses, and related judicial decisions.',
                'sensitivity_level' => 'very_high',
                'special_category' => true,
                'gdpr_article_reference' => 'Article 10',
                'processing_requirements' => 'Official authority or specific legal basis required.',
                'retention_requirements' => 'Strict retention periods regulated by law.',
                'security_requirements' => 'Maximum security measures and official authorization.',
                'sort_order' => 10,
            ],
            [
                'name' => 'political_opinions',
                'description' => 'Political affiliations, voting records, and political activities.',
                'sensitivity_level' => 'very_high',
                'special_category' => true,
                'gdpr_article_reference' => 'Article 9(1)',
                'processing_requirements' => 'Explicit consent or specific legal basis required.',
                'retention_requirements' => 'Limited retention periods with strict controls.',
                'security_requirements' => 'Maximum security measures and access controls.',
                'sort_order' => 11,
            ],
            [
                'name' => 'religious_beliefs',
                'description' => 'Religious affiliations, beliefs, and practices.',
                'sensitivity_level' => 'very_high',
                'special_category' => true,
                'gdpr_article_reference' => 'Article 9(1)',
                'processing_requirements' => 'Explicit consent or specific legal basis required.',
                'retention_requirements' => 'Limited retention periods with strict controls.',
                'security_requirements' => 'Maximum security measures and access controls.',
                'sort_order' => 12,
            ],
        ];

        foreach ($dataCategories as $category) {
            DataCategory::updateOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
}
