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
        Schema::create('consent_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->boolean('consent_given')->default(false);
            $table->string('purpose')->nullable();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->string('consentable_type')->nullable();
            $table->unsignedBigInteger('consentable_id')->nullable();
            $table->string('consent_type')->nullable();
            $table->string('consent_status')->nullable();
            $table->string('consent_method')->nullable();
            $table->dateTime('consent_date')->nullable();
            $table->dateTime('withdrawal_date')->nullable();
            $table->dateTime('expiry_date')->nullable();
            $table->string('consent_version')->nullable();
            $table->text('consent_text')->nullable();
            $table->string('consent_language')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('consent_source')->nullable();
            $table->string('consent_channel')->nullable();
            $table->string('consent_evidence')->nullable();
            $table->string('consent_evidence_file')->nullable();
            $table->text('consent_notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
            $table->index(['consentable_type', 'consentable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consent_records');
    }
};
