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
        Schema::create('data_breaches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->string('breach_number')->unique();
            $table->string('breach_type'); // unauthorized_access, data_loss, system_failure, human_error
            $table->enum('severity', ['low', 'medium', 'high', 'critical']);
            $table->enum('status', ['detected', 'investigating', 'contained', 'resolved', 'closed']);
            $table->dateTime('detection_date');
            $table->dateTime('notification_date')->nullable(); // when notified to DPA
            $table->dateTime('dpa_notification_date')->nullable();
            $table->integer('affected_data_subjects_count')->nullable();
            $table->json('affected_data_categories')->nullable();
            $table->text('breach_description');
            $table->text('containment_measures')->nullable();
            $table->text('remediation_actions')->nullable();
            $table->text('lessons_learned')->nullable();
            $table->text('impact_assessment')->nullable();
            $table->boolean('individuals_notified')->default(false);
            $table->dateTime('individuals_notification_date')->nullable();
            $table->text('notification_method')->nullable();
            $table->boolean('dpa_notified')->default(false);
            $table->text('dpa_notification_details')->nullable();
            $table->text('investigation_findings')->nullable();
            $table->text('corrective_actions')->nullable();
            $table->text('preventive_measures')->nullable();
            $table->decimal('estimated_financial_impact', 15, 2)->nullable();
            $table->text('legal_implications')->nullable();
            $table->text('insurance_claims')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['company_id', 'status']);
            $table->index(['detection_date']);
            $table->index(['severity']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_breaches');
    }
};
