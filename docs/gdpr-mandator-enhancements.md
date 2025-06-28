# GDPR Mandator Enhancements

This document describes the GDPR-specific enhancements made to the Mandator model to support agent-client relationships in GDPR compliance services.

## Overview

The mandator model has been enhanced to support your company's role as a GDPR compliance agent for other companies (clients). This allows you to track service agreements, compliance status, training requirements, and other GDPR-specific information for each client.

## New Fields Added

### Agent Relationship Fields
- `agent_company_id` - Your company providing GDPR services
- `gdpr_representative_id` - Your company's GDPR representative assigned to this client

### Service Agreement Fields
- `service_agreement_number` - Unique identifier for the service agreement
- `service_start_date` - When the GDPR service agreement started
- `service_end_date` - When the service agreement expires
- `service_status` - Current status (active, expired, terminated, pending_renewal)
- `service_type` - Type of GDPR service (gdpr_compliance, data_audit, dpo_services, training, consulting)

### GDPR Compliance Tracking
- `compliance_score` - 0-100 GDPR compliance score
- `last_gdpr_audit_date` - Date of last GDPR audit
- `next_gdpr_audit_date` - Date of next scheduled GDPR audit
- `gdpr_maturity_level` - Client's GDPR maturity (beginner, intermediate, advanced, expert)
- `risk_level` - Risk assessment level (low, medium, high, very_high)

### GDPR Service Scope
- `gdpr_services_provided` - Array of GDPR services provided to this client
- `gdpr_requirements` - Specific GDPR requirements for this client
- `applicable_regulations` - Array of applicable regulations (GDPR, CCPA, etc.)

### Communication Preferences
- `gdpr_reporting_frequency` - How often to send GDPR reports
- `gdpr_reporting_format` - Preferred report format
- `gdpr_reporting_recipients` - Additional recipients for GDPR reports

### Incident Management
- `last_data_incident_date` - Date of last data incident
- `data_incidents_count` - Total number of data incidents
- `incident_response_plan` - Incident response plan details

### Training and Awareness
- `last_gdpr_training_date` - Date of last GDPR training
- `next_gdpr_training_date` - Date of next scheduled training
- `employees_trained_count` - Number of employees trained
- `gdpr_training_required` - Whether training is required

### GDPR Documentation Status
- `privacy_policy_updated` - Whether privacy policy is up to date
- `privacy_policy_last_updated` - Date privacy policy was last updated
- `data_processing_register_maintained` - Whether data processing register is maintained
- `data_breach_procedures_established` - Whether breach procedures are established
- `data_subject_rights_procedures_established` - Whether data subject rights procedures are established

### Deadlines and Reminders
- `upcoming_gdpr_deadlines` - Array of upcoming GDPR deadlines
- `next_review_date` - Date of next compliance review
- `gdpr_notes` - General GDPR compliance notes

## New Relationships

### Agent Company
```php
public function agentCompany(): BelongsTo
{
    return $this->belongsTo(Company::class, 'agent_company_id');
}
```

### GDPR Representative
```php
public function gdprRepresentative(): BelongsTo
{
    return $this->belongsTo(User::class, 'gdpr_representative_id');
}
```

## New Scopes

### Active Services
```php
Mandator::activeServices() // Only active service agreements
```

### By Service Type
```php
Mandator::byServiceType('gdpr_compliance') // Filter by service type
```

### By Maturity Level
```php
Mandator::byMaturityLevel('intermediate') // Filter by GDPR maturity
```

### By Risk Level
```php
Mandator::byRiskLevel('high') // Filter by risk level
```

### Service Expiring Soon
```php
Mandator::serviceExpiringSoon(30) // Services expiring within 30 days
```

### Needs Training
```php
Mandator::needsTraining() // Clients needing GDPR training
```

### With Upcoming Deadlines
```php
Mandator::withUpcomingDeadlines(30) // Clients with deadlines within 30 days
```

## New Methods

### Service Agreement Status
```php
$mandator->service_days_remaining // Days until service expires
$mandator->isServiceExpiringSoon(30) // Check if expiring within 30 days
```

### Training Status
```php
$mandator->isTrainingOverdue() // Check if training is overdue
```

### Audit Status
```php
$mandator->isAuditOverdue() // Check if audit is overdue
```

### Compliance Status
```php
$mandator->gdpr_compliance_status // Returns: not_assessed, excellent, good, fair, poor
$mandator->hasCompleteGdprDocumentation() // Check if all documentation is in place
```

### UI Helpers
```php
$mandator->compliance_score_color // Returns color for UI (green, blue, yellow, red, gray)
$mandator->risk_level_color // Returns color for UI (green, yellow, orange, red, gray)
```

## Usage Examples

### Creating a New GDPR Client
```php
$mandator = Mandator::create([
    'company_id' => $clientCompany->id,
    'agent_company_id' => $yourCompany->id,
    'gdpr_representative_id' => $yourGdprRep->id,
    'first_name' => 'John',
    'last_name' => 'Doe',
    'email' => 'john.doe@client.com',
    'service_agreement_number' => 'GDPR-2024-001',
    'service_start_date' => '2024-01-01',
    'service_end_date' => '2024-12-31',
    'service_type' => 'gdpr_compliance',
    'gdpr_maturity_level' => 'beginner',
    'risk_level' => 'medium',
    'gdpr_services_provided' => ['compliance_audit', 'training', 'documentation'],
    'gdpr_training_required' => true,
]);
```

### Finding Clients Needing Attention
```php
// Clients with expiring services
$expiringClients = Mandator::serviceExpiringSoon(30)->get();

// Clients needing training
$trainingNeeded = Mandator::needsTraining()->get();

// High-risk clients
$highRiskClients = Mandator::byRiskLevel('high')->get();

// Clients with poor compliance scores
$poorCompliance = Mandator::where('compliance_score', '<', 60)->get();
```

### Updating Compliance Information
```php
$mandator->update([
    'compliance_score' => 85,
    'last_gdpr_audit_date' => now(),
    'next_gdpr_audit_date' => now()->addMonths(6),
    'privacy_policy_updated' => true,
    'privacy_policy_last_updated' => now(),
]);
```

## Benefits

1. **Service Agreement Tracking** - Monitor service agreements and renewal dates
2. **Compliance Monitoring** - Track GDPR compliance scores and audit schedules
3. **Risk Management** - Identify high-risk clients requiring attention
4. **Training Management** - Track training requirements and schedules
5. **Documentation Status** - Monitor GDPR documentation completeness
6. **Deadline Management** - Track upcoming GDPR deadlines and reviews
7. **Reporting** - Generate GDPR compliance reports for clients

## Next Steps

1. Update the mandator create/edit forms to include the new GDPR fields
2. Create GDPR compliance dashboard views
3. Add GDPR-specific reporting functionality
4. Implement automated reminders for deadlines and training
5. Create GDPR audit workflow integration
