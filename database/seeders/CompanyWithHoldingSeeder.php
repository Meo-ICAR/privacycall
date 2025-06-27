<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Holding;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanyWithHoldingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create holdings first
        $holdings = [
            [
                'name' => 'Alpha Group Holdings',
                'companies' => [
                    [
                        'name' => 'Alpha Tech Solutions',
                        'legal_name' => 'Alpha Tech Solutions Ltd.',
                        'email' => 'info@alphatech.com',
                        'phone' => '+1-555-0101',
                        'website' => 'https://alphatech.com',
                        'company_type' => 'employer',
                        'industry' => 'Technology',
                        'size' => 'large',
                        'address_line_1' => '123 Tech Street',
                        'city' => 'San Francisco',
                        'state' => 'CA',
                        'postal_code' => '94105',
                        'country' => 'USA',
                    ],
                    [
                        'name' => 'Alpha Digital Marketing',
                        'legal_name' => 'Alpha Digital Marketing Inc.',
                        'email' => 'hello@alphadigital.com',
                        'phone' => '+1-555-0102',
                        'website' => 'https://alphadigital.com',
                        'company_type' => 'supplier',
                        'industry' => 'Marketing',
                        'size' => 'medium',
                        'address_line_1' => '456 Marketing Ave',
                        'city' => 'Los Angeles',
                        'state' => 'CA',
                        'postal_code' => '90210',
                        'country' => 'USA',
                    ],
                    [
                        'name' => 'Alpha Consulting',
                        'legal_name' => 'Alpha Consulting Group LLC',
                        'email' => 'contact@alphaconsulting.com',
                        'phone' => '+1-555-0103',
                        'website' => 'https://alphaconsulting.com',
                        'company_type' => 'partner',
                        'industry' => 'Consulting',
                        'size' => 'medium',
                        'address_line_1' => '789 Business Blvd',
                        'city' => 'New York',
                        'state' => 'NY',
                        'postal_code' => '10001',
                        'country' => 'USA',
                    ]
                ]
            ],
            [
                'name' => 'Beta Corporation',
                'companies' => [
                    [
                        'name' => 'Beta Manufacturing',
                        'legal_name' => 'Beta Manufacturing Corp.',
                        'email' => 'sales@betamanufacturing.com',
                        'phone' => '+1-555-0201',
                        'website' => 'https://betamanufacturing.com',
                        'company_type' => 'employer',
                        'industry' => 'Manufacturing',
                        'size' => 'large',
                        'address_line_1' => '321 Factory Road',
                        'city' => 'Detroit',
                        'state' => 'MI',
                        'postal_code' => '48201',
                        'country' => 'USA',
                    ],
                    [
                        'name' => 'Beta Logistics',
                        'legal_name' => 'Beta Logistics Solutions',
                        'email' => 'info@betalogistics.com',
                        'phone' => '+1-555-0202',
                        'website' => 'https://betalogistics.com',
                        'company_type' => 'supplier',
                        'industry' => 'Logistics',
                        'size' => 'medium',
                        'address_line_1' => '654 Warehouse Lane',
                        'city' => 'Chicago',
                        'state' => 'IL',
                        'postal_code' => '60601',
                        'country' => 'USA',
                    ]
                ]
            ],
            [
                'name' => 'Gamma Enterprises',
                'companies' => [
                    [
                        'name' => 'Gamma Healthcare',
                        'legal_name' => 'Gamma Healthcare Systems',
                        'email' => 'info@gammahc.com',
                        'phone' => '+1-555-0301',
                        'website' => 'https://gammahc.com',
                        'company_type' => 'employer',
                        'industry' => 'Healthcare',
                        'size' => 'large',
                        'address_line_1' => '987 Medical Center Dr',
                        'city' => 'Boston',
                        'state' => 'MA',
                        'postal_code' => '02101',
                        'country' => 'USA',
                    ],
                    [
                        'name' => 'Gamma Pharmaceuticals',
                        'legal_name' => 'Gamma Pharmaceuticals Ltd.',
                        'email' => 'contact@gammapharma.com',
                        'phone' => '+1-555-0302',
                        'website' => 'https://gammapharma.com',
                        'company_type' => 'supplier',
                        'industry' => 'Pharmaceuticals',
                        'size' => 'medium',
                        'address_line_1' => '147 Research Park',
                        'city' => 'Philadelphia',
                        'state' => 'PA',
                        'postal_code' => '19101',
                        'country' => 'USA',
                    ],
                    [
                        'name' => 'Gamma Medical Devices',
                        'legal_name' => 'Gamma Medical Devices Inc.',
                        'email' => 'sales@gammamedical.com',
                        'phone' => '+1-555-0303',
                        'website' => 'https://gammamedical.com',
                        'company_type' => 'supplier',
                        'industry' => 'Medical Devices',
                        'size' => 'small',
                        'address_line_1' => '258 Innovation Way',
                        'city' => 'Austin',
                        'state' => 'TX',
                        'postal_code' => '73301',
                        'country' => 'USA',
                    ]
                ]
            ],
            [
                'name' => 'Delta Ventures',
                'companies' => [
                    [
                        'name' => 'Delta Fintech',
                        'legal_name' => 'Delta Financial Technologies',
                        'email' => 'hello@deltafintech.com',
                        'phone' => '+1-555-0401',
                        'website' => 'https://deltafintech.com',
                        'company_type' => 'employer',
                        'industry' => 'Financial Services',
                        'size' => 'medium',
                        'address_line_1' => '369 Wall Street',
                        'city' => 'New York',
                        'state' => 'NY',
                        'postal_code' => '10005',
                        'country' => 'USA',
                    ]
                ]
            ]
        ];

        // Create holdings and their companies
        foreach ($holdings as $holdingData) {
            // Create the holding
            $holding = Holding::firstOrCreate([
                'name' => $holdingData['name']
            ]);

            // Create companies for this holding
            foreach ($holdingData['companies'] as $companyData) {
                Company::firstOrCreate([
                    'name' => $companyData['name']
                ], array_merge($companyData, [
                    'holding_id' => $holding->id,
                    'is_active' => true,
                    'gdpr_consent_date' => now(),
                    'data_retention_period' => 365,
                    'data_processing_purpose' => 'Business operations and customer service',
                    'data_controller_contact' => 'privacy@' . strtolower(str_replace(' ', '', $companyData['name'])) . '.com',
                    'data_protection_officer' => 'DPO@' . strtolower(str_replace(' ', '', $companyData['name'])) . '.com',
                ]));
            }
        }

        // Create some standalone companies (not part of any holding)
        $standaloneCompanies = [
            [
                'name' => 'Independent Startup',
                'legal_name' => 'Independent Startup LLC',
                'email' => 'info@independentstartup.com',
                'phone' => '+1-555-0501',
                'website' => 'https://independentstartup.com',
                'company_type' => 'employer',
                'industry' => 'Technology',
                'size' => 'small',
                'address_line_1' => '111 Startup Street',
                'city' => 'Seattle',
                'state' => 'WA',
                'postal_code' => '98101',
                'country' => 'USA',
            ],
            [
                'name' => 'Freelance Consulting',
                'legal_name' => 'Freelance Consulting Services',
                'email' => 'contact@freelanceconsulting.com',
                'phone' => '+1-555-0502',
                'website' => 'https://freelanceconsulting.com',
                'company_type' => 'supplier',
                'industry' => 'Consulting',
                'size' => 'small',
                'address_line_1' => '222 Freelance Ave',
                'city' => 'Portland',
                'state' => 'OR',
                'postal_code' => '97201',
                'country' => 'USA',
            ]
        ];

        foreach ($standaloneCompanies as $companyData) {
            Company::firstOrCreate([
                'name' => $companyData['name']
            ], array_merge($companyData, [
                'holding_id' => null, // No holding
                'is_active' => true,
                'gdpr_consent_date' => now(),
                'data_retention_period' => 365,
                'data_processing_purpose' => 'Business operations and customer service',
                'data_controller_contact' => 'privacy@' . strtolower(str_replace(' ', '', $companyData['name'])) . '.com',
                'data_protection_officer' => 'DPO@' . strtolower(str_replace(' ', '', $companyData['name'])) . '.com',
            ]));
        }

        $this->command->info('Companies with holdings seeded successfully!');
        $this->command->info('Created ' . Holding::count() . ' holdings');
        $this->command->info('Created ' . Company::count() . ' companies');
    }
}
