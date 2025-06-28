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
        Schema::table('audit_requests', function (Blueprint $table) {
            // Compliance and risk assessment fields
            $table->integer('compliance_score')->nullable()->after('notes');
            $table->enum('risk_level', ['low', 'medium', 'high', 'critical'])->nullable()->after('compliance_score');
            $table->enum('audit_frequency', ['monthly', 'quarterly', 'semi_annual', 'annual', 'biennial'])->nullable()->after('risk_level');
            $table->date('next_audit_date')->nullable()->after('audit_frequency');

            // Audit findings and actions
            $table->json('audit_findings')->nullable()->after('next_audit_date');
            $table->json('corrective_actions')->nullable()->after('audit_findings');

            // Audit execution details
            $table->decimal('audit_cost', 10, 2)->nullable()->after('corrective_actions');
            $table->decimal('audit_duration_hours', 5, 2)->nullable()->after('audit_cost');
            $table->foreignId('auditor_assigned')->nullable()->constrained('users')->nullOnDelete()->after('audit_duration_hours');

            // Supplier response tracking
            $table->date('supplier_response_deadline')->nullable()->after('auditor_assigned');
            $table->boolean('supplier_response_received')->default(false)->after('supplier_response_deadline');

            // Audit reporting
            $table->string('audit_report_url')->nullable()->after('supplier_response_received');

            // Certification tracking
            $table->string('certification_status')->nullable()->after('audit_report_url');
            $table->date('certification_expiry_date')->nullable()->after('certification_status');

            // Indexes for better performance
            $table->index(['company_id', 'risk_level']);
            $table->index(['supplier_id', 'risk_level']);
            $table->index(['next_audit_date']);
            $table->index(['supplier_response_deadline']);
            $table->index(['certification_expiry_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audit_requests', function (Blueprint $table) {
            $table->dropForeign(['auditor_assigned']);
            $table->dropIndex(['company_id', 'risk_level']);
            $table->dropIndex(['supplier_id', 'risk_level']);
            $table->dropIndex(['next_audit_date']);
            $table->dropIndex(['supplier_response_deadline']);
            $table->dropIndex(['certification_expiry_date']);

            $table->dropColumn([
                'compliance_score',
                'risk_level',
                'audit_frequency',
                'next_audit_date',
                'audit_findings',
                'corrective_actions',
                'audit_cost',
                'audit_duration_hours',
                'auditor_assigned',
                'supplier_response_deadline',
                'supplier_response_received',
                'audit_report_url',
                'certification_status',
                'certification_expiry_date',
            ]);
        });
    }
};
