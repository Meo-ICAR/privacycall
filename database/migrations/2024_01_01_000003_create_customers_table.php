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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('customer_number')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('address_line_1');
            $table->string('address_line_2')->nullable();
            $table->string('city');
            $table->string('state')->nullable();
            $table->string('postal_code');
            $table->string('country');
            $table->enum('customer_type', ['individual', 'business'])->default('individual');
            $table->enum('customer_status', ['active', 'inactive', 'suspended'])->default('active');
            $table->date('customer_since');
            $table->date('last_purchase_date')->nullable();
            $table->decimal('total_purchases', 12, 2)->default(0.00);
            $table->enum('preferred_contact_method', ['email', 'phone', 'mail', 'sms'])->default('email');
            $table->json('marketing_preferences')->nullable();

            // GDPR Compliance Fields
            $table->timestamp('gdpr_consent_date')->nullable();
            $table->boolean('data_processing_consent')->default(false);
            $table->boolean('marketing_consent')->default(false);
            $table->boolean('third_party_sharing_consent')->default(false);
            $table->boolean('data_retention_consent')->default(false);
            $table->boolean('right_to_be_forgotten_requested')->default(false);
            $table->timestamp('right_to_be_forgotten_date')->nullable();
            $table->boolean('data_portability_requested')->default(false);
            $table->timestamp('data_portability_date')->nullable();
            $table->text('data_processing_purpose')->nullable();
            $table->integer('data_retention_period')->default(730); // Default 2 years in days

            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance and GDPR compliance (shortened names)
            $table->index(['company_id', 'is_active'], 'customer_company_active_idx');
            $table->index(['customer_type', 'customer_status'], 'customer_type_status_idx');
            $table->index(['gdpr_consent_date'], 'customer_gdpr_consent_idx');
            $table->index(['customer_since'], 'customer_since_idx');
            $table->index(['right_to_be_forgotten_requested'], 'customer_rtbf_idx');
            $table->index(['data_portability_requested'], 'customer_dp_idx');
            $table->index(['email'], 'customer_email_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
