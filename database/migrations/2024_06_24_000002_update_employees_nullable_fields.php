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
        Schema::table('employees', function (Blueprint $table) {
            $table->string('employee_number')->nullable()->change();
            $table->string('first_name')->nullable()->change();
            $table->string('last_name')->nullable()->change();
            $table->string('email')->nullable()->change();
            $table->string('position')->nullable()->change();
            $table->string('department')->nullable()->change();
            $table->date('hire_date')->nullable()->change();
            $table->date('termination_date')->nullable()->change();
            $table->decimal('salary', 10, 2)->nullable()->change();
            $table->string('work_location')->nullable()->change();
            $table->string('emergency_contact_name')->nullable()->change();
            $table->string('emergency_contact_phone')->nullable()->change();
            $table->string('emergency_contact_relationship')->nullable()->change();
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
