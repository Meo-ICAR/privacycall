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
        Schema::table('customers', function (Blueprint $table) {
            // Consent Acquisition Fields
            $table->enum('consent_method', [
                'web_form', 'email', 'phone', 'in_person', 'document', 'app_notification'
            ])->nullable()->after('gdpr_consent_date');

            $table->enum('consent_source', [
                'website', 'mobile_app', 'call_center', 'in_store', 'email_campaign', 'contract'
            ])->nullable()->after('consent_method');

            $table->enum('consent_channel', [
                'online', 'offline', 'phone', 'email'
            ])->nullable()->after('consent_source');

            $table->enum('consent_evidence', [
                'screenshot', 'document', 'audio_recording', 'video_recording', 'email_confirmation', 'digital_signature'
            ])->nullable()->after('consent_channel');

            $table->string('consent_evidence_file')->nullable()->after('consent_evidence');
            $table->string('consent_text', 5000)->nullable()->after('consent_evidence_file');
            $table->string('consent_language', 10)->nullable()->after('consent_text');
            $table->string('consent_version', 50)->nullable()->after('consent_language');
            $table->ipAddress('ip_address')->nullable()->after('consent_version');
            $table->string('user_agent', 500)->nullable()->after('ip_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn([
                'consent_method',
                'consent_source',
                'consent_channel',
                'consent_evidence',
                'consent_evidence_file',
                'consent_text',
                'consent_language',
                'consent_version',
                'ip_address',
                'user_agent'
            ]);
        });
    }
};
