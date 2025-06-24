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
            $table->string('first_name')->nullable()->change();
            $table->string('last_name')->nullable()->change();
            $table->string('email')->nullable()->change();
            $table->string('address_line_1')->nullable()->change();
            $table->string('city')->nullable()->change();
            $table->string('postal_code')->nullable()->change();
            $table->string('country')->nullable()->change();
            $table->enum('customer_type', ['individual', 'business'])->nullable()->default('individual')->change();
            $table->enum('customer_status', ['active', 'inactive', 'suspended'])->nullable()->default('active')->change();
            $table->date('customer_since')->nullable()->change();
            $table->decimal('total_purchases', 12, 2)->nullable()->default(0.00)->change();
            $table->enum('preferred_contact_method', ['email', 'phone', 'mail', 'sms'])->nullable()->default('email')->change();
            $table->boolean('data_processing_consent')->nullable()->default(false)->change();
            $table->boolean('marketing_consent')->nullable()->default(false)->change();
            $table->boolean('third_party_sharing_consent')->nullable()->default(false)->change();
            $table->boolean('data_retention_consent')->nullable()->default(false)->change();
            $table->boolean('right_to_be_forgotten_requested')->nullable()->default(false)->change();
            $table->boolean('data_portability_requested')->nullable()->default(false)->change();
            $table->integer('data_retention_period')->nullable()->default(730)->change();
            $table->boolean('is_active')->nullable()->default(true)->change();
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
