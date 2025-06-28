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
        Schema::table('representatives', function (Blueprint $table) {
            // Add logo URL field
            $table->string('logo_url')->nullable()->after('department');

            // Add original_id field to track cloned records
            $table->foreignId('original_id')->nullable()->after('logo_url')->constrained('representatives')->nullOnDelete();

            // Add index for better performance when querying cloned records
            $table->index('original_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('representatives', function (Blueprint $table) {
            $table->dropForeign(['original_id']);
            $table->dropIndex(['original_id']);
            $table->dropColumn(['logo_url', 'original_id']);
        });
    }
};
