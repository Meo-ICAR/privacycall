<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('mandators', function (Blueprint $table) {
            // Agent relationship fields
            $table->foreignId('agent_company_id')->nullable()->after('company_id')->constrained('companies')->comment('Your company as the GDPR compliance agent');
            $table->foreignId('gdpr_representative_id')->nullable()->after('agent_company_id')->constrained('users')->comment('Your company\'s GDPR representative');

            // GDPR service agreement fields
            $table->string('service_agreement_number')->nullable()->after('gdpr_representative_id');
            $table->date('service_start_date')->nullable()->after('service_agreement_number');
            $table->date('service_end_date')->nullable()->after('service_start_date');
            $table->enum('service_status', ['active', 'expired', 'terminated', 'pending_renewal'])->default('active')->after('service_end_date');
            $table->enum('service_type', ['gdpr_compliance', 'data_audit', 'dpo_services', 'training', 'consulting'])->nullable()->after('service_status');

            // GDPR compliance tracking
            $table->integer('compliance_score')->nullable()->after('service_type')->comment('0-100 GDPR compliance score');
            $table->date('last_gdpr_audit_date')->nullable()->after('compliance_score');
            $table->date('next_gdpr_audit_date')->nullable()->after('last_gdpr_audit_date');
            $table->enum('gdpr_maturity_level', ['beginner', 'intermediate', 'advanced', 'expert'])->nullable()->after('next_gdpr_audit_date');
            $table->enum('risk_level', ['low', 'medium', 'high', 'very_high'])->default('medium')->after('gdpr_maturity_level');

            // GDPR service scope
            $table->json('gdpr_services_provided')->nullable()->after('risk_level')->comment('Array of GDPR services provided');
            $table->text('gdpr_requirements')->nullable()->after('gdpr_services_provided')->comment('Specific GDPR requirements for this client');
            $table->json('applicable_regulations')->nullable()->after('gdpr_requirements')->comment('Array of applicable regulations');

            // Communication preferences for GDPR matters
            $table->enum('gdpr_reporting_frequency', ['monthly', 'quarterly', 'annually', 'on_demand'])->nullable()->after('applicable_regulations');
            $table->enum('gdpr_reporting_format', ['pdf', 'excel', 'web_dashboard', 'email'])->default('pdf')->after('gdpr_reporting_frequency');
            $table->json('gdpr_reporting_recipients')->nullable()->after('gdpr_reporting_format')->comment('Additional recipients for GDPR reports');

            // GDPR incident management
            $table->date('last_data_incident_date')->nullable()->after('gdpr_reporting_recipients');
            $table->integer('data_incidents_count')->default(0)->after('last_data_incident_date');
            $table->text('incident_response_plan')->nullable()->after('data_incidents_count');

            // GDPR training and awareness
            $table->date('last_gdpr_training_date')->nullable()->after('incident_response_plan');
            $table->date('next_gdpr_training_date')->nullable()->after('last_gdpr_training_date');
            $table->integer('employees_trained_count')->nullable()->after('next_gdpr_training_date');
            $table->boolean('gdpr_training_required')->default(true)->after('employees_trained_count');

            // GDPR documentation
            $table->boolean('privacy_policy_updated')->default(false)->after('gdpr_training_required');
            $table->date('privacy_policy_last_updated')->nullable()->after('privacy_policy_updated');
            $table->boolean('data_processing_register_maintained')->default(false)->after('privacy_policy_last_updated');
            $table->boolean('data_breach_procedures_established')->default(false)->after('data_processing_register_maintained');
            $table->boolean('data_subject_rights_procedures_established')->default(false)->after('data_breach_procedures_established');

            // GDPR deadlines and reminders
            $table->json('upcoming_gdpr_deadlines')->nullable()->after('data_subject_rights_procedures_established');
            $table->date('next_review_date')->nullable()->after('upcoming_gdpr_deadlines');
            $table->text('gdpr_notes')->nullable()->after('next_review_date')->comment('General GDPR compliance notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mandators', function (Blueprint $table) {
            $table->dropForeign(['agent_company_id']);
            $table->dropForeign(['gdpr_representative_id']);

            $table->dropColumn([
                'agent_company_id',
                'gdpr_representative_id',
                'service_agreement_number',
                'service_start_date',
                'service_end_date',
                'service_status',
                'service_type',
                'compliance_score',
                'last_gdpr_audit_date',
                'next_gdpr_audit_date',
                'gdpr_maturity_level',
                'risk_level',
                'gdpr_services_provided',
                'gdpr_requirements',
                'applicable_regulations',
                'gdpr_reporting_frequency',
                'gdpr_reporting_format',
                'gdpr_reporting_recipients',
                'last_data_incident_date',
                'data_incidents_count',
                'incident_response_plan',
                'last_gdpr_training_date',
                'next_gdpr_training_date',
                'employees_trained_count',
                'gdpr_training_required',
                'privacy_policy_updated',
                'privacy_policy_last_updated',
                'data_processing_register_maintained',
                'data_breach_procedures_established',
                'data_subject_rights_procedures_established',
                'upcoming_gdpr_deadlines',
                'next_review_date',
                'gdpr_notes'
            ]);
        });
    }
};
