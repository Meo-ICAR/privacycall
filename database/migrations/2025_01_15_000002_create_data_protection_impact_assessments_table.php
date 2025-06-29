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
        Schema::create('data_protection_i_as', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('data_processing_activity_id')->nullable()->constrained('data_processing_activities')->nullOnDelete();
            $table->string('dpia_number')->unique();
            $table->date('assessment_date');
            $table->enum('assessment_status', ['draft', 'in_progress', 'completed', 'approved', 'rejected', 'under_review']);
            $table->enum('risk_level', ['low', 'medium', 'high', 'very_high']);
            $table->text('processing_purpose');
            $table->json('data_categories_processed')->nullable();
            $table->json('data_subjects_affected')->nullable();
            $table->text('necessity_and_proportionality');
            $table->text('risk_mitigation_measures')->nullable();
            $table->text('residual_risks')->nullable();
            $table->text('dpo_opinion')->nullable();
            $table->text('stakeholder_consultation')->nullable();
            $table->date('approval_date')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('review_frequency')->nullable();
            $table->date('next_review_date')->nullable();
            $table->text('methodology_used')->nullable();
            $table->text('identified_risks')->nullable();
            $table->text('risk_assessment_criteria')->nullable();
            $table->text('consultation_findings')->nullable();
            $table->text('recommendations')->nullable();
            $table->text('implementation_plan')->nullable();
            $table->boolean('supervisory_authority_consultation_required')->default(false);
            $table->date('supervisory_consultation_date')->nullable();
            $table->text('supervisory_authority_feedback')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['company_id', 'assessment_status']);
            $table->index(['risk_level']);
            $table->index(['next_review_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_protection_impact_assessments');
    }
};
