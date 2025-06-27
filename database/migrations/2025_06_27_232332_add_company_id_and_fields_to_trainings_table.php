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
        Schema::table('trainings', function (Blueprint $table) {
            if (!Schema::hasColumn('trainings', 'company_id')) {
                $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
            }
            if (!Schema::hasColumn('trainings', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }
            if (!Schema::hasColumn('trainings', 'notes')) {
                $table->text('notes')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trainings', function (Blueprint $table) {
            if (Schema::hasColumn('trainings', 'company_id')) {
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            }
            if (Schema::hasColumn('trainings', 'is_active')) {
                $table->dropColumn('is_active');
            }
            if (Schema::hasColumn('trainings', 'notes')) {
                $table->dropColumn('notes');
            }
        });
    }
};
