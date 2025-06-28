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
        // First, drop the foreign key constraint on original_id
        Schema::table('representatives', function (Blueprint $table) {
            $table->dropForeign(['original_id']);
        });

        // Rename the table
        Schema::rename('representatives', 'mandators');

        // Re-add the foreign key constraint with the new table name
        Schema::table('mandators', function (Blueprint $table) {
            $table->foreign('original_id')->references('id')->on('mandators')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the foreign key constraint
        Schema::table('mandators', function (Blueprint $table) {
            $table->dropForeign(['original_id']);
        });

        // Rename the table back
        Schema::rename('mandators', 'representatives');

        // Re-add the foreign key constraint with the original table name
        Schema::table('representatives', function (Blueprint $table) {
            $table->foreign('original_id')->references('id')->on('representatives')->nullOnDelete();
        });
    }
};
