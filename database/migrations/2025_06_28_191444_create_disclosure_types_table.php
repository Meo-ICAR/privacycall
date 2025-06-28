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
        Schema::create('disclosure_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g., 'gdpr_updates'
            $table->string('display_name'); // e.g., 'GDPR Updates'
            $table->text('description')->nullable(); // Description of what this disclosure type covers
            $table->string('category')->default('general'); // e.g., 'compliance', 'security', 'privacy', 'general'
            $table->boolean('is_active')->default(true); // Whether this disclosure type is available for selection
            $table->integer('sort_order')->default(0); // For ordering in dropdowns/lists
            $table->timestamps();

            // Indexes
            $table->index(['category', 'is_active']);
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disclosure_types');
    }
};
