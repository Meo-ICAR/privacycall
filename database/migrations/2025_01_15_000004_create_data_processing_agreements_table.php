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
        Schema::create('data_processing_agreements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade'); // data controller
            $table->foreignId('processor_company_id')->constrained('companies')->onDelete('cascade'); // data processor
            $table->string('agreement_number')->unique();
            $table->date('agreement_date');
            $table->enum('agreement_status', ['draft', 'active', 'expired', 'terminated', 'under_review']);
            $table->text('processing_purposes');
            $table->json('data_categories_processed')->nullable();
            $table->json('data_subjects_affected')->nullable();
            $table->string('processing_duration');
            $table->text('security_measures');
            $table->text('data_breach_notification_requirements');
            $table->text('data_subject_rights_assistance');
            $table->text('audit_rights');
            $table->boolean('sub_processor_authorization')->default(false);
            $table->json('sub_processors_list')->nullable();
            $table->text('data_return_deletion_obligations');
            $table->text('liability_provisions');
            $table->text('termination_conditions');
            $table->date('renewal_date')->nullable();
            $table->text('confidentiality_obligations')->nullable();
            $table->text('data_quality_obligations')->nullable();
            $table->text('record_keeping_requirements')->nullable();
            $table->text('cooperation_with_supervisory_authority')->nullable();
            $table->text('insurance_requirements')->nullable();
            $table->text('dispute_resolution_procedures')->nullable();
            $table->text('governing_law')->nullable();
            $table->text('jurisdiction')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['company_id', 'agreement_status']);
            $table->index(['processor_company_id']);
            $table->index(['agreement_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_processing_agreements');
    }
};
