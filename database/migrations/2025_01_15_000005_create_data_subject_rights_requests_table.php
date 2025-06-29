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
        Schema::create('data_subject_rights_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->enum('request_type', ['access', 'rectification', 'erasure', 'portability', 'restriction', 'objection', 'automated_decision_making']);
            $table->enum('requester_type', ['customer', 'employee', 'supplier', 'other']);
            $table->unsignedBigInteger('requester_id')->nullable();
            $table->string('request_number')->unique();
            $table->dateTime('request_date');
            $table->dateTime('response_deadline');
            $table->enum('status', ['received', 'processing', 'completed', 'rejected', 'extended', 'cancelled']);
            $table->string('identity_verification_method')->nullable();
            $table->boolean('verification_status')->default(false);
            $table->text('request_description');
            $table->json('data_categories_requested')->nullable();
            $table->boolean('response_provided')->default(false);
            $table->dateTime('response_date')->nullable();
            $table->string('response_method')->nullable();
            $table->text('response_content')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('extension_reason')->nullable();
            $table->integer('extension_days')->nullable();
            $table->text('processing_notes')->nullable();
            $table->text('data_provided')->nullable();
            $table->text('rectification_made')->nullable();
            $table->text('erasure_confirmation')->nullable();
            $table->text('portability_format')->nullable();
            $table->text('restriction_applied')->nullable();
            $table->text('objection_handled')->nullable();
            $table->text('automated_decision_review')->nullable();
            $table->boolean('third_party_notification_required')->default(false);
            $table->text('third_party_notification_details')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['company_id', 'status']);
            $table->index(['request_type']);
            $table->index(['request_date']);
            $table->index(['response_deadline']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_subject_rights_requests');
    }
};
