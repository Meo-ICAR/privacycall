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
        Schema::create('disclosure_type_mandator', function (Blueprint $table) {
            $table->id();
            $table->foreignId('disclosure_type_id')->constrained()->onDelete('cascade');
            $table->foreignId('mandator_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            // Ensure unique combinations
            $table->unique(['disclosure_type_id', 'mandator_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disclosure_type_mandator');
    }
};
