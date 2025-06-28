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
        Schema::create('email_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Gmail, Microsoft, OVH, Aruba, Libero, etc.
            $table->string('display_name'); // Human-readable name
            $table->string('type'); // imap, pop3, gmail_api, microsoft_graph, etc.
            $table->string('icon')->nullable(); // Provider icon/logo
            $table->string('color')->nullable(); // Brand color for UI

            // IMAP/POP3 Configuration
            $table->string('imap_host')->nullable();
            $table->integer('imap_port')->nullable();
            $table->string('imap_encryption')->nullable(); // ssl, tls, none
            $table->string('pop3_host')->nullable();
            $table->integer('pop3_port')->nullable();
            $table->string('pop3_encryption')->nullable();

            // SMTP Configuration for sending
            $table->string('smtp_host')->nullable();
            $table->integer('smtp_port')->nullable();
            $table->string('smtp_encryption')->nullable();
            $table->boolean('smtp_auth_required')->default(true);

            // API Configuration (for Gmail, Microsoft Graph, etc.)
            $table->string('api_endpoint')->nullable();
            $table->string('api_version')->nullable();
            $table->string('oauth_client_id')->nullable();
            $table->string('oauth_client_secret')->nullable();
            $table->string('oauth_redirect_uri')->nullable();
            $table->json('oauth_scopes')->nullable();

            // Connection settings
            $table->integer('timeout')->default(30);
            $table->boolean('verify_ssl')->default(true);
            $table->string('auth_type')->default('password'); // password, oauth, api_key

            // Provider-specific settings
            $table->json('settings')->nullable(); // Additional provider-specific settings

            // Status and metadata
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->text('setup_instructions')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('name');
            $table->index('type');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_providers');
    }
};
