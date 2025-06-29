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
        Schema::create('security_measures', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description');
            $table->enum('category', ['technical', 'organizational', 'physical', 'administrative']);
            $table->enum('effectiveness_level', ['low', 'medium', 'high', 'very_high']);
            $table->text('implementation_guidance')->nullable();
            $table->text('cost_considerations')->nullable();
            $table->text('maintenance_requirements')->nullable();
            $table->text('compliance_standards')->nullable();
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
        Schema::dropIfExists('security_measures');
    }
};
