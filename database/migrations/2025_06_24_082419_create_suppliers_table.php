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
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->string('supplier_number')->nullable();
            $table->string('name');
            $table->string('legal_name')->nullable();
            $table->string('registration_number')->nullable();
            $table->string('vat_number')->nullable();
            $table->string('contact_person_name')->nullable();
            $table->string('contact_person_email')->nullable();
            $table->string('contact_person_phone')->nullable();
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('supplier_type')->nullable();
            $table->string('supplier_category')->nullable();
            $table->string('supplier_status')->nullable();
            $table->date('supplier_since')->nullable();
            $table->date('last_order_date')->nullable();
            $table->integer('total_orders')->nullable();
            $table->decimal('total_spent', 15, 2)->nullable();
            $table->string('payment_terms')->nullable();
            $table->decimal('credit_limit', 15, 2)->nullable();
            $table->text('bank_account_info')->nullable();
            $table->text('tax_info')->nullable();
            $table->dateTime('gdpr_consent_date')->nullable();
            $table->boolean('data_processing_consent')->default(false);
            $table->boolean('third_party_sharing_consent')->default(false);
            $table->boolean('data_retention_consent')->default(false);
            $table->boolean('right_to_be_forgotten_requested')->default(false);
            $table->dateTime('right_to_be_forgotten_date')->nullable();
            $table->boolean('data_portability_requested')->default(false);
            $table->dateTime('data_portability_date')->nullable();
            $table->text('data_processing_purpose')->nullable();
            $table->integer('data_retention_period')->nullable();
            $table->boolean('data_processing_agreement_signed')->default(false);
            $table->dateTime('data_processing_agreement_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
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
