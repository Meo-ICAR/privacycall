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
        Schema::create('processing_register_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->string('version_number')->unique(); // es. "v1.0.0", "v1.1.0"
            $table->string('version_name')->nullable(); // es. "Initial Version", "Q1 2024 Update"
            $table->text('version_description')->nullable();
            $table->enum('status', ['draft', 'active', 'archived', 'superseded'])->default('draft');
            $table->date('effective_date')->nullable(); // Data di entrata in vigore
            $table->date('expiry_date')->nullable(); // Data di scadenza (quando viene sostituita)
            $table->json('register_data')->nullable(); // Snapshot completo del registro
            $table->json('activities_summary')->nullable(); // Riepilogo attività
            $table->json('compliance_summary')->nullable(); // Riepilogo compliance
            $table->json('changes_log')->nullable(); // Log delle modifiche rispetto alla versione precedente
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('approved_at')->nullable();
            $table->text('approval_notes')->nullable();
            $table->boolean('is_current')->default(false); // Indica se è la versione corrente
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'is_current']);
            $table->index(['effective_date']);
            $table->index(['version_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('processing_register_versions');
    }
};
