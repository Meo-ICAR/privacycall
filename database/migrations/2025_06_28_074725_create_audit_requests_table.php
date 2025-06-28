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
        Schema::create('audit_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // Representative who sent the request
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete(); // Target supplier
            $table->string('audit_type'); // compliance, security, gdpr, financial, operational
            $table->string('audit_scope'); // full, partial, specific_area
            $table->string('status')->default('pending'); // pending, in_progress, completed, cancelled
            $table->string('priority')->default('normal'); // low, normal, high, urgent
            $table->string('subject');
            $table->longText('message');
            $table->json('requested_documents')->nullable(); // Array of requested documents
            $table->json('received_documents')->nullable(); // Array of received documents
            $table->date('requested_deadline')->nullable(); // When documents are due
            $table->date('scheduled_date')->nullable(); // Scheduled audit date
            $table->time('scheduled_time')->nullable(); // Scheduled audit time
            $table->string('meeting_type')->nullable(); // call, visit, video_conference
            $table->string('meeting_link')->nullable(); // For virtual meetings
            $table->string('meeting_location')->nullable(); // For physical visits
            $table->text('notes')->nullable(); // Additional notes
            $table->json('follow_up_dates')->nullable(); // Array of follow-up dates
            $table->timestamp('last_follow_up')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            // Indexes for better performance
            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'audit_type']);
            $table->index(['supplier_id', 'status']);
            $table->index('scheduled_date');
            $table->index('requested_deadline');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_requests');
    }
};
