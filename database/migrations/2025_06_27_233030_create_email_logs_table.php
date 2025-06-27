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
        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('recipient_email');
            $table->string('recipient_name')->nullable();
            $table->string('subject');
            $table->text('body');
            $table->string('template_name')->nullable();
            $table->enum('status', ['pending', 'sent', 'delivered', 'failed', 'bounced'])->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable(); // Additional data like supplier_id, etc.
            $table->timestamps();

            $table->index(['company_id', 'status']);
            $table->index(['recipient_email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_logs');
    }
};
