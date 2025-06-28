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
        Schema::create('representatives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('position')->nullable();
            $table->string('department')->nullable();

            // Disclosure subscription information
            $table->json('disclosure_subscriptions')->nullable(); // Array of subscribed disclosure types
            $table->dateTime('last_disclosure_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();

            // Contact preferences
            $table->boolean('email_notifications')->default(true);
            $table->boolean('sms_notifications')->default(false);
            $table->string('preferred_contact_method')->default('email'); // email, phone, sms

            $table->softDeletes();
            $table->timestamps();

            // Indexes for better performance
            $table->index(['company_id', 'is_active']);
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('representatives');
    }
};
