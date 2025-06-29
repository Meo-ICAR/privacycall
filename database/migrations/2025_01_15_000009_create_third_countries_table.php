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
        Schema::create('third_countries', function (Blueprint $table) {
            $table->id();
            $table->string('country_name');
            $table->string('country_code', 10)->unique();
            $table->boolean('adequacy_decision')->default(false);
            $table->date('adequacy_decision_date')->nullable();
            $table->string('adequacy_decision_reference')->nullable();
            $table->text('adequacy_decision_details')->nullable();
            $table->enum('risk_level', ['low', 'medium', 'high', 'very_high']);
            $table->text('risk_assessment')->nullable();
            $table->text('data_protection_laws')->nullable();
            $table->text('supervisory_authority')->nullable();
            $table->text('contact_information')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('third_countries');
    }
};
