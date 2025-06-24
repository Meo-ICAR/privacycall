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
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('customer_type_id')->nullable()->constrained('customer_types')->nullOnDelete();
            $table->string('customer_number')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->nullable();
            $table->string('customer_type')->nullable();
            $table->string('customer_status')->nullable();
            $table->date('customer_since')->nullable();
            $table->date('last_purchase_date')->nullable();
            $table->decimal('total_purchases', 15, 2)->nullable();
            $table->string('preferred_contact_method')->nullable();
            $table->text('marketing_preferences')->nullable();
            $table->dateTime('gdpr_consent_date')->nullable();
            $table->boolean('data_processing_consent')->default(false);
            $table->boolean('marketing_consent')->default(false);
            $table->boolean('third_party_sharing_consent')->default(false);
            $table->boolean('data_retention_consent')->default(false);
            $table->boolean('right_to_be_forgotten_requested')->default(false);
            $table->dateTime('right_to_be_forgotten_date')->nullable();
            $table->boolean('data_portability_requested')->default(false);
            $table->dateTime('data_portability_date')->nullable();
            $table->text('data_processing_purpose')->nullable();
            $table->integer('data_retention_period')->nullable();
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
        Schema::dropIfExists('customers');
    }
};
