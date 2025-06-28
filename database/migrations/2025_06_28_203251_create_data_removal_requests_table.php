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
        Schema::create('data_removal_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('cascade');
            $table->foreignId('mandator_id')->nullable()->constrained('mandators')->onDelete('cascade');
            $table->foreignId('requested_by_user_id')->nullable()->constrained('users')->onDelete('set null');

            // Request Details
            $table->string('request_number')->unique();
            $table->enum('request_type', ['customer_direct', 'mandator_request', 'legal_obligation', 'system_cleanup']);
            $table->enum('status', ['pending', 'in_review', 'approved', 'rejected', 'completed', 'cancelled'])->default('pending');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');

            // Request Information
            $table->text('reason_for_removal');
            $table->text('data_categories_to_remove')->nullable(); // JSON field for specific data categories
            $table->text('retention_justification')->nullable(); // If retention is needed
            $table->text('legal_basis_for_retention')->nullable();

            // Dates
            $table->date('request_date');
            $table->date('due_date')->nullable();
            $table->date('review_date')->nullable();
            $table->date('completion_date')->nullable();

            // Processing Details
            $table->text('review_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('completion_notes')->nullable();
            $table->text('data_removal_method')->nullable();

            // Verification
            $table->boolean('identity_verified')->default(false);
            $table->text('verification_method')->nullable();
            $table->text('verification_notes')->nullable();

            // Compliance
            $table->boolean('gdpr_compliant')->default(true);
            $table->text('compliance_notes')->nullable();
            $table->boolean('notify_third_parties')->default(false);
            $table->text('third_party_notification_details')->nullable();

            // Audit Trail
            $table->foreignId('reviewed_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('completed_by_user_id')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();

            // Indexes
            $table->index(['company_id', 'status']);
            $table->index(['customer_id', 'status']);
            $table->index(['request_date', 'due_date']);
            $table->index('request_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_removal_requests');
    }
};
