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
        Schema::create('third_country_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('data_processing_activity_id')->nullable()->constrained('data_processing_activities')->nullOnDelete();
            $table->string('transfer_number')->unique();
            $table->string('destination_country');
            $table->text('transfer_purpose');
            $table->json('data_categories_transferred')->nullable();
            $table->integer('number_of_data_subjects')->nullable();
            $table->string('legal_basis'); // adequacy_decision, appropriate_safeguards, binding_corporate_rules, etc.
            $table->text('safeguards_implemented')->nullable();
            $table->string('adequacy_decision_reference')->nullable();
            $table->boolean('standard_contractual_clauses_used')->default(false);
            $table->string('scc_version')->nullable();
            $table->text('scc_details')->nullable();
            $table->boolean('binding_corporate_rules')->default(false);
            $table->string('bcr_reference')->nullable();
            $table->text('bcr_details')->nullable();
            $table->string('certification_mechanism')->nullable();
            $table->string('certification_reference')->nullable();
            $table->string('transfer_frequency'); // one_time, recurring, continuous
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->enum('risk_assessment_level', ['low', 'medium', 'high']);
            $table->boolean('supervisory_authority_consultation')->default(false);
            $table->date('consultation_date')->nullable();
            $table->text('consultation_outcome')->nullable();
            $table->text('risk_mitigation_measures')->nullable();
            $table->text('monitoring_procedures')->nullable();
            $table->text('audit_procedures')->nullable();
            $table->boolean('data_subjects_informed')->default(false);
            $table->text('information_provided_to_subjects')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['company_id', 'destination_country']);
            $table->index(['legal_basis']);
            $table->index(['risk_assessment_level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('third_country_transfers');
    }
};
