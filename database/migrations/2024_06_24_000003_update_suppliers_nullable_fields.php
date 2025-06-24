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
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('name')->nullable()->change();
            $table->string('legal_name')->nullable()->change();
            $table->string('registration_number')->nullable()->change();
            $table->string('vat_number')->nullable()->change();
            $table->string('contact_person_name')->nullable()->change();
            $table->string('contact_person_email')->nullable()->change();
            $table->string('contact_person_phone')->nullable()->change();
            $table->string('address_line_1')->nullable()->change();
            $table->string('address_line_2')->nullable()->change();
            $table->string('city')->nullable()->change();
            $table->string('state')->nullable()->change();
            $table->string('postal_code')->nullable()->change();
            $table->string('country')->nullable()->change();
            $table->string('phone')->nullable()->change();
            $table->string('email')->nullable()->change();
            $table->string('website')->nullable()->change();
            $table->enum('supplier_type', ['manufacturer', 'distributor', 'wholesaler', 'retailer', 'service'])->nullable()->change();
            $table->enum('supplier_status', ['active', 'inactive', 'suspended'])->nullable()->change();
            $table->date('supplier_since')->nullable()->change();
            $table->date('last_order_date')->nullable()->change();
            $table->decimal('total_orders', 12, 2)->nullable()->change();
            $table->string('payment_terms')->nullable()->change();
            $table->decimal('credit_limit', 12, 2)->nullable()->change();
            $table->text('bank_account_info')->nullable()->change();
            $table->text('tax_info')->nullable()->change();
            $table->boolean('is_active')->nullable()->default(true)->change();
            $table->text('notes')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No down migration for nullable changes
    }
};
