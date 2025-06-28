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
        Schema::create('compliance_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete(); // Company being audited
            $table->foreignId('mandator_id')->constrained('mandators')->cascadeOnDelete(); // Mandator requesting the audit
            $table->string('request_type'); // compliance, security, gdpr, financial, operational, data_processing
            $table->string('request_scope'); // full, partial, specific_area, document_only
            $table->string('status')->default('pending'); // pending, in_progress, completed, cancelled, overdue
            $table->string('priority')->default('normal'); // low, normal, high, urgent
            $table->string('subject');
            $table->longText('message');
            $table->json('requested_documents')->nullable(); // Array of requested documents
            $table->json('provided_documents')->nullable(); // Array of provided documents
            $table->date('requested_deadline')->nullable(); // When documents are due
            $table->date('scheduled_date')->nullable(); // Scheduled audit/meeting date
            $table->time('scheduled_time')->nullable(); // Scheduled audit/meeting time
            $table->string('meeting_type')->nullable(); // call, visit, video_conference, document_review
            $table->string('meeting_link')->nullable(); // For virtual meetings
            $table->string('meeting_location')->nullable(); // For physical meetings
            $table->text('notes')->nullable(); // Additional notes
            $table->json('follow_up_dates')->nullable(); // Array of follow-up dates
            $table->timestamp('last_follow_up')->nullable(); // Last follow-up date
            $table->timestamp('completed_at')->nullable(); // When request was completed

            // Response tracking
            $table->boolean('response_sent')->default(false); // Whether company has responded
            $table->timestamp('response_sent_at')->nullable(); // When response was sent
            $table->text('response_message')->nullable(); // Company's response message

            // Document tracking
            $table->boolean('documents_uploaded')->default(false); // Whether documents were uploaded
            $table->timestamp('documents_uploaded_at')->nullable(); // When documents were uploaded
            $table->integer('documents_count')->default(0); // Number of documents provided

            // Compliance assessment
            $table->integer('compliance_score')->nullable(); // 0-100 score
            $table->enum('risk_level', ['low', 'medium', 'high', 'critical'])->nullable();
            $table->json('compliance_findings')->nullable(); // Findings from mandator
            $table->json('required_actions')->nullable(); // Actions required by company

            // Assignment
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete(); // User assigned to handle this request

            $table->timestamps();

            // Indexes
            $table->index(['company_id', 'status']);
            $table->index(['mandator_id', 'status']);
            $table->index(['requested_deadline', 'status']);
            $table->index(['priority', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compliance_requests');
    }
};
