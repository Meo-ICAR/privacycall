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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('supplier_number')->unique();
            $table->string('name');
            $table->string('legal_name')->nullable();
            $table->string('registration_number')->nullable()->unique();
            $table->string('vat_number')->nullable()->unique();
            $table->string('contact_person_name')->nullable();
            $table->string('contact_person_email')->nullable();
            $table->string('contact_person_phone')->nullable();
            $table->string('address_line_1');
            $table->string('address_line_2')->nullable();
            $table->string('city');
            $table->string('state')->nullable();
            $table->string('postal_code');
            $table->string('country');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->enum('supplier_type', ['goods', 'services', 'both'])->default('goods');
            $table->enum('supplier_category', ['primary', 'secondary', 'emergency'])->default('primary');
            $table->enum('supplier_status', ['active', 'inactive', 'suspended', 'approved', 'pending'])->default('pending');
            $table->date('supplier_since');
            $table->date('last_order_date')->nullable();
            $table->integer('total_orders')->default(0);
            $table->decimal('total_spent', 12, 2)->default(0.00);
            $table->string('payment_terms')->nullable();
            $table->decimal('credit_limit', 12, 2)->nullable();
            $table->text('bank_account_info')->nullable();
            $table->text('tax_info')->nullable();

            // GDPR Compliance Fields
            $table->timestamp('gdpr_consent_date')->nullable();
            $table->boolean('data_processing_consent')->default(false);
            $table->boolean('third_party_sharing_consent')->default(false);
            $table->boolean('data_retention_consent')->default(false);
            $table->boolean('right_to_be_forgotten_requested')->default(false);
            $table->timestamp('right_to_be_forgotten_date')->nullable();
            $table->boolean('data_portability_requested')->default(false);
            $table->timestamp('data_portability_date')->nullable();
            $table->text('data_processing_purpose')->nullable();
            $table->integer('data_retention_period')->default(730); // Default 2 years in days
            $table->boolean('data_processing_agreement_signed')->default(false);
            $table->timestamp('data_processing_agreement_date')->nullable();

            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance and GDPR compliance (shortened names)
            $table->index(['company_id', 'is_active'], 'supplier_company_active_idx');
            $table->index(['supplier_type', 'supplier_status'], 'supplier_type_status_idx');
            $table->index(['supplier_category', 'is_active'], 'supplier_category_active_idx');
            $table->index(['gdpr_consent_date'], 'supplier_gdpr_consent_idx');
            $table->index(['supplier_since'], 'supplier_since_idx');
            $table->index(['right_to_be_forgotten_requested'], 'supplier_rtbf_idx');
            $table->index(['data_portability_requested'], 'supplier_dp_idx');
            $table->index(['data_processing_agreement_signed'], 'supplier_dpa_signed_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
