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
        Schema::create('data_processing_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('processable_type'); // Company, Employee, Customer, Supplier
            $table->unsignedBigInteger('processable_id');
            $table->string('activity_name');
            $table->text('activity_description');
            $table->string('processing_purpose');
            $table->enum('legal_basis', [
                'consent',
                'contract',
                'legal_obligation',
                'vital_interests',
                'public_task',
                'legitimate_interests'
            ])->default('consent');
            $table->json('data_categories')->nullable(); // personal_data, sensitive_data, special_categories
            $table->json('data_subjects')->nullable(); // employees, customers, suppliers, visitors
            $table->json('data_recipients')->nullable(); // internal, external, third_parties
            $table->json('third_country_transfers')->nullable();
            $table->integer('retention_period')->default(730); // Default 2 years in days
            $table->json('security_measures')->nullable();
            $table->enum('risk_assessment_level', ['low', 'medium', 'high'])->default('low');
            $table->boolean('data_protection_impact_assessment_required')->default(false);
            $table->timestamp('data_protection_impact_assessment_date')->nullable();
            $table->boolean('data_protection_officer_consulted')->default(false);
            $table->timestamp('data_protection_officer_consultation_date')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance and GDPR compliance (shortened names)
            $table->index(['company_id', 'is_active'], 'dpa_company_active_idx');
            $table->index(['processable_type', 'processable_id'], 'dpa_processable_idx');
            $table->index(['legal_basis'], 'dpa_legal_basis_idx');
            $table->index(['risk_assessment_level'], 'dpa_risk_level_idx');
            $table->index(['processing_purpose'], 'dpa_purpose_idx');
            $table->index(['start_date', 'end_date'], 'dpa_dates_idx');
            $table->index(['data_protection_impact_assessment_required'], 'dpa_dpia_required_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_processing_activities');
    }
};
