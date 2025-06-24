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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('role', ['superadmin', 'admin', 'manager', 'employee', 'customer'])->default('employee');
            $table->boolean('is_active')->default(true);

            // GDPR Compliance Fields
            $table->timestamp('gdpr_consent_date')->nullable();
            $table->boolean('data_processing_consent')->default(false);
            $table->boolean('marketing_consent')->default(false);
            $table->boolean('third_party_sharing_consent')->default(false);
            $table->boolean('data_retention_consent')->default(false);
            $table->boolean('right_to_be_forgotten_requested')->default(false);
            $table->timestamp('right_to_be_forgotten_date')->nullable();
            $table->boolean('data_portability_requested')->default(false);
            $table->timestamp('data_portability_date')->nullable();

            $table->softDeletes();

            // Indexes for performance and GDPR compliance
            $table->index(['company_id', 'is_active'], 'user_company_active_idx');
            $table->index(['role', 'is_active'], 'user_role_active_idx');
            $table->index(['gdpr_consent_date'], 'user_gdpr_consent_idx');
            $table->index(['right_to_be_forgotten_requested'], 'user_rtbf_idx');
            $table->index(['data_portability_requested'], 'user_dp_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropIndex(['company_id', 'is_active']);
            $table->dropIndex(['role', 'is_active']);
            $table->dropIndex(['gdpr_consent_date']);
            $table->dropIndex(['right_to_be_forgotten_requested']);
            $table->dropIndex(['data_portability_requested']);

            $table->dropColumn([
                'company_id',
                'role',
                'is_active',
                'gdpr_consent_date',
                'data_processing_consent',
                'marketing_consent',
                'third_party_sharing_consent',
                'data_retention_consent',
                'right_to_be_forgotten_requested',
                'right_to_be_forgotten_date',
                'data_portability_requested',
                'data_portability_date',
                'deleted_at'
            ]);
        });
    }
};
