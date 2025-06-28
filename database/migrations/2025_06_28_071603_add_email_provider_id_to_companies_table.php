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
        Schema::table('companies', function (Blueprint $table) {
            $table->foreignId('email_provider_id')->nullable()->constrained('email_providers')->nullOnDelete();
            $table->json('email_credentials')->nullable(); // Encrypted credentials for the email account
            $table->boolean('email_configured')->default(false); // Whether email is properly configured
            $table->timestamp('email_last_sync')->nullable(); // Last successful email sync
            $table->text('email_sync_error')->nullable(); // Last sync error message
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropForeign(['email_provider_id']);
            $table->dropColumn(['email_provider_id', 'email_credentials', 'email_configured', 'email_last_sync', 'email_sync_error']);
        });
    }
};
