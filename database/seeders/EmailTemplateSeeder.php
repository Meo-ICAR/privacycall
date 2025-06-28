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
                ],
                // Audit-specific templates
                [
                    'name' => 'Compliance Audit Request',
                    'subject' => 'Compliance Audit Request - {{company_name}}',
                    'body' => "Dear {{supplier_name}},

We are writing to inform you that we will be conducting a compliance audit of your organization as part of our ongoing supplier assessment program.

**Audit Details:**
- **Type:** Compliance Audit
- **Scope:** {{audit_scope}}
- **Priority:** {{priority}}

**Purpose:**
This audit is designed to ensure that your organization continues to meet our compliance requirements and maintains the high standards we expect from our suppliers.

**Requested Documents:**
{{requested_documents}}

**Deadline for Document Submission:** {{requested_deadline}}

**Scheduled Meeting:**
{{scheduled_date}} at {{scheduled_time}}
Type: {{meeting_type}}
{{meeting_location}}

**Next Steps:**
1. Please review the requested documents and prepare them for submission
2. Confirm your availability for the scheduled meeting
3. Contact us if you have any questions or need clarification

{{custom_message}}

We appreciate your cooperation in this process. If you have any questions or concerns, please don't hesitate to contact us.

Best regards,
{{user_name}}
{{company_name}}",
                    'category' => 'audit',
                    'variables' => [
                        'supplier_name' => 'Supplier Name',
                        'supplier_email' => 'Supplier Email',
                        'audit_scope' => 'Audit Scope (Full/Partial/Specific Area)',
                        'priority' => 'Priority Level',
                        'requested_documents' => 'List of Requested Documents',
                        'requested_deadline' => 'Document Submission Deadline',
                        'scheduled_date' => 'Scheduled Audit Date',
                        'scheduled_time' => 'Scheduled Audit Time',
                        'meeting_type' => 'Meeting Type (Call/Visit/Video Conference)',
                        'meeting_location' => 'Meeting Location',
                        'company_name' => 'Your Company Name',
                        'user_name' => 'Current User Name',
                        'custom_message' => 'Custom Message'
                    ]
                ],
                [
                    'name' => 'GDPR Audit Request',
                    'subject' => 'GDPR Compliance Audit Request - {{company_name}}',
                    'body' => "Dear {{supplier_name}},

We are conducting a GDPR compliance audit to ensure that your data processing activities align with current data protection regulations and our contractual requirements.

**Audit Details:**
- **Type:** GDPR Compliance Audit
- **Scope:** {{audit_scope}}
- **Priority:** {{priority}}

**Focus Areas:**
- Data processing activities and legal basis
- Data subject rights implementation
- Data retention and deletion policies
- Security measures and breach procedures
- Third-party data sharing practices
- Consent management processes

**Requested Documents:**
{{requested_documents}}

**Deadline for Document Submission:** {{requested_deadline}}

**Scheduled Meeting:**
{{scheduled_date}} at {{scheduled_time}}
Type: {{meeting_type}}
{{meeting_location}}

**Required Information:**
1. Data Processing Register
2. Privacy Policy and Data Protection Impact Assessments
3. Data Subject Rights Procedures
4. Security and Breach Notification Procedures
5. Data Processing Agreements with third parties
6. Consent management records

{{custom_message}}

This audit is crucial for maintaining our compliance with GDPR requirements. Please ensure all requested information is provided by the deadline.

Best regards,
{{user_name}}
{{company_name}}",
                    'category' => 'audit',
                    'variables' => [
                        'supplier_name' => 'Supplier Name',
                        'supplier_email' => 'Supplier Email',
                        'audit_scope' => 'Audit Scope (Full/Partial/Specific Area)',
                        'priority' => 'Priority Level',
                        'requested_documents' => 'List of Requested Documents',
                        'requested_deadline' => 'Document Submission Deadline',
                        'scheduled_date' => 'Scheduled Audit Date',
                        'scheduled_time' => 'Scheduled Audit Time',
                        'meeting_type' => 'Meeting Type (Call/Visit/Video Conference)',
                        'meeting_location' => 'Meeting Location',
                        'company_name' => 'Your Company Name',
                        'user_name' => 'Current User Name',
                        'custom_message' => 'Custom Message'
                    ]
                ],
                [
                    'name' => 'Security Audit Request',
                    'subject' => 'Security Audit Request - {{company_name}}',
                    'body' => "Dear {{supplier_name}},

We are conducting a security audit to assess your organization's security posture and ensure that appropriate measures are in place to protect sensitive information.

**Audit Details:**
- **Type:** Security Audit
- **Scope:** {{audit_scope}}
- **Priority:** {{priority}}

**Security Assessment Areas:**
- Access control and authentication systems
- Data encryption and protection measures
- Network security and monitoring
- Incident response and recovery procedures
- Employee security awareness and training
- Physical security controls

**Requested Documents:**
{{requested_documents}}

**Deadline for Document Submission:** {{requested_deadline}}

**Scheduled Meeting:**
{{scheduled_date}} at {{scheduled_time}}
Type: {{meeting_type}}
{{meeting_location}}

**Required Information:**
1. Security policies and procedures
2. Access control documentation
3. Incident response plans
4. Security training records
5. Penetration testing reports
6. Security audit reports

{{custom_message}}

Please ensure that all security-related documentation is up to date and readily available for review during the audit.

Best regards,
{{user_name}}
{{company_name}}",
                    'category' => 'audit',
                    'variables' => [
                        'supplier_name' => 'Supplier Name',
                        'supplier_email' => 'Supplier Email',
                        'audit_scope' => 'Audit Scope (Full/Partial/Specific Area)',
                        'priority' => 'Priority Level',
                        'requested_documents' => 'List of Requested Documents',
                        'requested_deadline' => 'Document Submission Deadline',
                        'scheduled_date' => 'Scheduled Audit Date',
                        'scheduled_time' => 'Scheduled Audit Time',
                        'meeting_type' => 'Meeting Type (Call/Visit/Video Conference)',
                        'meeting_location' => 'Meeting Location',
                        'company_name' => 'Your Company Name',
                        'user_name' => 'Current User Name',
                        'custom_message' => 'Custom Message'
                    ]
                ],
                [
                    'name' => 'Follow-up Audit Request',
                    'subject' => 'Follow-up Audit Request - {{company_name}}',
                    'body' => "Dear {{supplier_name}},

Following our previous audit conducted on {{previous_audit_date}}, we are scheduling a follow-up audit to review the implementation of the recommendations and address any outstanding items.

**Follow-up Audit Details:**
- **Type:** Follow-up Audit
- **Scope:** Review of previous findings and recommendations
- **Priority:** {{priority}}

**Previous Audit Findings:**
{{previous_findings}}

**Current Status Review:**
- Implementation of previous recommendations
- Progress on corrective actions
- Verification of compliance improvements
- Assessment of ongoing compliance status

**Requested Documents:**
{{requested_documents}}

**Deadline for Document Submission:** {{requested_deadline}}

**Scheduled Meeting:**
{{scheduled_date}} at {{scheduled_time}}
Type: {{meeting_type}}
{{meeting_location}}

**Preparation Required:**
1. Status update on previous audit findings
2. Evidence of implemented corrective actions
3. Updated policies and procedures
4. Progress reports on ongoing improvements

{{custom_message}}

We look forward to reviewing your progress and continuing our collaborative relationship to ensure ongoing compliance.

Best regards,
{{user_name}}
{{company_name}}",
                    'category' => 'audit',
                    'variables' => [
                        'supplier_name' => 'Supplier Name',
                        'supplier_email' => 'Supplier Email',
                        'previous_audit_date' => 'Previous Audit Date',
                        'previous_findings' => 'Previous Audit Findings',
                        'priority' => 'Priority Level',
                        'requested_documents' => 'List of Requested Documents',
                        'requested_deadline' => 'Document Submission Deadline',
                        'scheduled_date' => 'Scheduled Audit Date',
                        'scheduled_time' => 'Scheduled Audit Time',
                        'meeting_type' => 'Meeting Type (Call/Visit/Video Conference)',
                        'meeting_location' => 'Meeting Location',
                        'company_name' => 'Your Company Name',
                        'user_name' => 'Current User Name',
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
