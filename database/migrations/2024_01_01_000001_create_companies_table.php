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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('legal_name')->nullable();
            $table->string('registration_number')->nullable()->unique();
            $table->string('vat_number')->nullable()->unique();
            $table->string('address_line_1');
            $table->string('address_line_2')->nullable();
            $table->string('city');
            $table->string('state')->nullable();
            $table->string('postal_code');
            $table->string('country');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->enum('company_type', ['employer', 'customer', 'supplier', 'partner'])->default('customer');
            $table->string('industry')->nullable();
            $table->enum('size', ['small', 'medium', 'large'])->default('small');

            // GDPR Compliance Fields
            $table->timestamp('gdpr_consent_date')->nullable();
            $table->integer('data_retention_period')->default(730); // Default 2 years in days
            $table->text('data_processing_purpose')->nullable();
            $table->string('data_controller_contact')->nullable();
            $table->string('data_protection_officer')->nullable();

            // Status and Metadata
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance and GDPR compliance
            $table->index(['company_type', 'is_active']);
            $table->index(['gdpr_consent_date']);
            $table->index(['country', 'is_active']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
