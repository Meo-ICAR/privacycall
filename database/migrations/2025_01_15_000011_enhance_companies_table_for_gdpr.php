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
        Schema::table('companies', function (Blueprint $table) {
            // Informazioni GDPR specifiche
            $table->enum('gdpr_compliance_status', ['compliant', 'non_compliant', 'under_review'])->default('under_review')->after('notes');
            $table->boolean('dpo_appointed')->default(false)->after('gdpr_compliance_status');
            $table->date('dpo_appointment_date')->nullable()->after('dpo_appointed');
            $table->string('dpo_contact_email')->nullable()->after('dpo_appointment_date');
            $table->string('dpo_contact_phone')->nullable()->after('dpo_contact_email');

            // Autorità di controllo
            $table->string('supervisory_authority_name')->nullable()->after('dpo_contact_phone');
            $table->text('supervisory_authority_contact')->nullable()->after('supervisory_authority_name');

            // Politiche e procedure
            $table->string('privacy_policy_version')->nullable()->after('supervisory_authority_contact');
            $table->date('privacy_policy_last_updated')->nullable()->after('privacy_policy_version');
            $table->string('data_retention_policy_version')->nullable()->after('privacy_policy_last_updated');
            $table->date('data_retention_policy_last_updated')->nullable()->after('data_retention_policy_version');

            // Audit e compliance
            $table->date('last_gdpr_audit_date')->nullable()->after('data_retention_policy_last_updated');
            $table->date('next_gdpr_audit_date')->nullable()->after('last_gdpr_audit_date');
            $table->text('gdpr_audit_findings')->nullable()->after('next_gdpr_audit_date');
            $table->text('compliance_improvement_plan')->nullable()->after('gdpr_audit_findings');

            // Incidenti e violazioni
            $table->integer('data_breach_count')->default(0)->after('compliance_improvement_plan');
            $table->date('last_data_breach_date')->nullable()->after('data_breach_count');
            $table->text('data_breach_response_procedures')->nullable()->after('last_data_breach_date');

            // Trasferimenti internazionali
            $table->integer('international_transfers_count')->default(0)->after('data_breach_response_procedures');
            $table->json('adequacy_decisions_used')->nullable()->after('international_transfers_count');
            $table->boolean('standard_contractual_clauses_used')->default(false)->after('adequacy_decisions_used');

            // Indici aggiuntivi
            $table->index(['gdpr_compliance_status']);
            $table->index(['dpo_appointed']);
            $table->index(['next_gdpr_audit_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            // Rimuovi gli indici
            $table->dropIndex(['gdpr_compliance_status']);
            $table->dropIndex(['dpo_appointed']);
            $table->dropIndex(['next_gdpr_audit_date']);

            // Rimuovi le colonne
            $table->dropColumn([
                'gdpr_compliance_status',
                'dpo_appointed',
                'dpo_appointment_date',
                'dpo_contact_email',
                'dpo_contact_phone',
                'supervisory_authority_name',
                'supervisory_authority_contact',
                'privacy_policy_version',
                'privacy_policy_last_updated',
                'data_retention_policy_version',
                'data_retention_policy_last_updated',
                'last_gdpr_audit_date',
                'next_gdpr_audit_date',
                'gdpr_audit_findings',
                'compliance_improvement_plan',
                'data_breach_count',
                'last_data_breach_date',
                'data_breach_response_procedures',
                'international_transfers_count',
                'adequacy_decisions_used',
                'standard_contractual_clauses_used'
            ]);
        });
    }
};
