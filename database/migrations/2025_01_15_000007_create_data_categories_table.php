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
        Schema::create('data_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description');
            $table->enum('sensitivity_level', ['low', 'medium', 'high', 'very_high']);
            $table->boolean('special_category')->default(false);
            $table->text('gdpr_article_reference')->nullable();
            $table->text('processing_requirements')->nullable();
            $table->text('retention_requirements')->nullable();
            $table->text('security_requirements')->nullable();
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
        Schema::dropIfExists('data_categories');
    }
};
