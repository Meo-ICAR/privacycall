<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use App\Models\Company;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::all();

        foreach ($companies as $company) {
            $templates = [
                [
                    'name' => 'Supplier Welcome',
                    'subject' => 'Welcome to {{company_name}} - Supplier Onboarding',
                    'body' => "Dear {{supplier_name}},

Welcome to {{company_name}}! We're excited to have you as one of our valued suppliers.

This email confirms that your supplier account has been set up in our system. You can expect to receive regular communications from us regarding orders, updates, and important information.

If you have any questions or need assistance, please don't hesitate to contact us.

{{custom_message}}

Best regards,
{{user_name}}
{{company_name}}",
                    'category' => 'supplier',
                    'variables' => [
                        'supplier_name' => 'Supplier Name',
                        'supplier_email' => 'Supplier Email',
                        'supplier_phone' => 'Supplier Phone',
                        'company_name' => 'Your Company Name',
                        'user_name' => 'Current User Name',
                        'current_date' => 'Current Date',
                        'custom_message' => 'Custom Message'
                    ]
                ],
                [
                    'name' => 'Supplier Update',
                    'subject' => 'Important Update from {{company_name}}',
                    'body' => "Dear {{supplier_name}},

We hope this email finds you well. We wanted to share some important updates with you regarding our partnership.

{{custom_message}}

Please review this information carefully and let us know if you have any questions or concerns.

Thank you for your continued partnership.

Best regards,
{{user_name}}
{{company_name}}",
                    'category' => 'supplier',
                    'variables' => [
                        'supplier_name' => 'Supplier Name',
                        'supplier_email' => 'Supplier Email',
                        'supplier_phone' => 'Supplier Phone',
                        'company_name' => 'Your Company Name',
                        'user_name' => 'Current User Name',
                        'current_date' => 'Current Date',
                        'custom_message' => 'Custom Message'
                    ]
                ],
                [
                    'name' => 'Supplier Request',
                    'subject' => 'Request from {{company_name}}',
                    'body' => "Dear {{supplier_name}},

We hope you're doing well. We have a request that we'd like to discuss with you.

{{custom_message}}

Please let us know your thoughts and availability for further discussion.

Thank you for your time and consideration.

Best regards,
{{user_name}}
{{company_name}}",
                    'category' => 'supplier',
                    'variables' => [
                        'supplier_name' => 'Supplier Name',
                        'supplier_email' => 'Supplier Email',
                        'supplier_phone' => 'Supplier Phone',
                        'company_name' => 'Your Company Name',
                        'user_name' => 'Current User Name',
                        'current_date' => 'Current Date',
                        'custom_message' => 'Custom Message'
                    ]
                ],
                [
                    'name' => 'GDPR Compliance Request',
                    'subject' => 'GDPR Compliance Information Request - {{company_name}}',
                    'body' => "Dear {{supplier_name}},

As part of our ongoing commitment to data protection and GDPR compliance, we are reaching out to all our suppliers to ensure we have the most up-to-date information about your data processing practices.

{{custom_message}}

Please provide the following information:
1. Your current data processing activities
2. Data retention policies
3. Security measures in place
4. Contact details for your Data Protection Officer (if applicable)

This information will help us maintain our compliance records and ensure we meet our regulatory obligations.

Thank you for your cooperation.

Best regards,
{{user_name}}
{{company_name}}",
                    'category' => 'supplier',
                    'variables' => [
                        'supplier_name' => 'Supplier Name',
                        'supplier_email' => 'Supplier Email',
                        'supplier_phone' => 'Supplier Phone',
                        'company_name' => 'Your Company Name',
                        'user_name' => 'Current User Name',
                        'current_date' => 'Current Date',
                        'custom_message' => 'Custom Message'
                    ]
                ]
            ];

            foreach ($templates as $template) {
                EmailTemplate::firstOrCreate([
                    'company_id' => $company->id,
                    'name' => $template['name'],
                    'category' => $template['category']
                ], [
                    'subject' => $template['subject'],
                    'body' => $template['body'],
                    'variables' => $template['variables'],
                    'is_active' => true
                ]);
            }
        }
    }
}
