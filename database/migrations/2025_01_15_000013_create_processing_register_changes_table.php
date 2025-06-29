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
        Schema::create('processing_reg_cs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('processing_register_version_id')->nullable()->constrained('processing_register_versions')->nullOnDelete();
            $table->string('change_type'); // created, updated, deleted, status_changed
            $table->string('entity_type'); // data_processing_activity, data_breach, dpia, etc.
            $table->unsignedBigInteger('entity_id')->nullable(); // ID dell'entità modificata
            $table->string('entity_name')->nullable(); // Nome dell'entità per riferimento
            $table->json('old_values')->nullable(); // Valori precedenti
            $table->json('new_values')->nullable(); // Nuovi valori
            $table->text('change_description'); // Descrizione della modifica
            $table->text('change_reason')->nullable(); // Motivo della modifica
            $table->enum('impact_level', ['low', 'medium', 'high', 'critical'])->default('low');
            $table->boolean('requires_review')->default(false); // Se richiede revisione
            $table->boolean('requires_approval')->default(false); // Se richiede approvazione
            $table->foreignId('changed_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            $table->enum('review_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->boolean('is_approved')->default(false);
            $table->dateTime('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('approval_notes')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'change_type']);
            $table->index(['company_id', 'entity_type']);
            $table->index(['company_id', 'impact_level']);
            $table->index(['company_id', 'requires_review']);
            $table->index(['changed_by']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('processing_register_changes');
    }
};
