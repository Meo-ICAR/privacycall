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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('employee_number')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('position')->nullable();
            $table->string('department')->nullable();
            $table->date('hire_date')->nullable();
            $table->date('termination_date')->nullable();
            $table->decimal('salary', 15, 2)->nullable();
            $table->string('employment_type')->nullable();
            $table->string('work_location')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('emergency_contact_relationship')->nullable();
            $table->dateTime('gdpr_consent_date')->nullable();
            $table->boolean('data_processing_consent')->default(false);
            $table->boolean('marketing_consent')->default(false);
            $table->boolean('third_party_sharing_consent')->default(false);
            $table->boolean('data_retention_consent')->default(false);
            $table->boolean('right_to_be_forgotten_requested')->default(false);
            $table->dateTime('right_to_be_forgotten_date')->nullable();
            $table->boolean('data_portability_requested')->default(false);
            $table->dateTime('data_portability_date')->nullable();
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
        Schema::dropIfExists('employees');
    }
};
