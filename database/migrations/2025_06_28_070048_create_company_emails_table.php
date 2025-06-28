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
        Schema::create('company_emails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // Admin who handled the email
            $table->string('email_id')->unique(); // External email ID (IMAP/Gmail API)
            $table->string('thread_id')->nullable(); // Email thread/conversation ID
            $table->string('from_email');
            $table->string('from_name')->nullable();
            $table->string('to_email'); // Company's data_controller_contact email
            $table->string('subject');
            $table->longText('body');
            $table->longText('body_plain')->nullable(); // Plain text version
            $table->json('attachments')->nullable(); // Array of attachment info
            $table->json('headers')->nullable(); // Email headers
            $table->timestamp('received_at');
            $table->timestamp('read_at')->nullable();
            $table->timestamp('replied_at')->nullable();
            $table->string('status')->default('unread'); // unread, read, replied, archived
            $table->string('priority')->default('normal'); // low, normal, high, urgent
            $table->json('labels')->nullable(); // Email labels/tags
            $table->text('notes')->nullable(); // Admin notes about the email
            $table->boolean('is_gdpr_related')->default(false); // Flag for GDPR-related emails
            $table->string('category')->nullable(); // complaint, inquiry, request, notification
            $table->timestamps();

            // Indexes for better performance
            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'received_at']);
            $table->index(['user_id', 'status']);
            $table->index('thread_id');
            $table->index('is_gdpr_related');
            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_emails');
    }
};
