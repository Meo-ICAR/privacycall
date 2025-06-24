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
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->string('processable_type')->nullable();
            $table->unsignedBigInteger('processable_id')->nullable();
            $table->string('activity_name');
            $table->text('description')->nullable();
            $table->string('purpose')->nullable();
            $table->text('activity_description')->nullable();
            $table->string('processing_purpose')->nullable();
            $table->string('legal_basis')->nullable();
            $table->json('data_categories')->nullable();
            $table->json('data_subjects')->nullable();
            $table->json('data_recipients')->nullable();
            $table->json('third_country_transfers')->nullable();
            $table->integer('retention_period')->nullable();
            $table->json('security_measures')->nullable();
            $table->string('risk_assessment_level')->nullable();
            $table->boolean('data_protection_impact_assessment_required')->default(false);
            $table->dateTime('data_protection_impact_assessment_date')->nullable();
            $table->boolean('data_protection_officer_consulted')->default(false);
            $table->dateTime('data_protection_officer_consultation_date')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->index(['processable_type', 'processable_id']);
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
