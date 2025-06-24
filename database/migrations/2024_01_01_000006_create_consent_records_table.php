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
        Schema::create('consent_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('consentable_type'); // Company, Employee, Customer, Supplier
            $table->unsignedBigInteger('consentable_id');
            $table->enum('consent_type', [
                'data_processing',
                'marketing',
                'third_party_sharing',
                'data_retention'
            ])->default('data_processing');
            $table->enum('consent_status', [
                'granted',
                'withdrawn',
                'expired',
                'pending'
            ])->default('pending');
            $table->enum('consent_method', [
                'web_form',
                'email',
                'phone',
                'in_person',
                'document'
            ])->default('web_form');
            $table->timestamp('consent_date')->nullable();
            $table->timestamp('withdrawal_date')->nullable();
            $table->timestamp('expiry_date')->nullable();
            $table->string('consent_version')->nullable();
            $table->text('consent_text')->nullable();
            $table->string('consent_language')->default('en');
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->enum('consent_source', [
                'website',
                'mobile_app',
                'call_center',
                'in_store'
            ])->default('website');
            $table->enum('consent_channel', [
                'online',
                'offline',
                'phone',
                'email'
            ])->default('online');
            $table->enum('consent_evidence', [
                'screenshot',
                'document',
                'audio_recording',
                'video_recording'
            ])->nullable();
            $table->string('consent_evidence_file')->nullable();
            $table->text('consent_notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance and GDPR compliance (shortened names)
            $table->index(['company_id', 'is_active'], 'cr_company_active_idx');
            $table->index(['consentable_type', 'consentable_id'], 'cr_consentable_idx');
            $table->index(['consent_type', 'consent_status'], 'cr_type_status_idx');
            $table->index(['consent_date'], 'cr_consent_date_idx');
            $table->index(['expiry_date'], 'cr_expiry_date_idx');
            $table->index(['withdrawal_date'], 'cr_withdrawal_date_idx');
            $table->index(['consent_method'], 'cr_method_idx');
            $table->index(['consent_source'], 'cr_source_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consent_records');
    }
};
