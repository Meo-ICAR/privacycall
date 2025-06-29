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
        // Aggiungi campi di versioning a data_processing_activities
        Schema::table('data_processing_activities', function (Blueprint $table) {
            $table->string('version')->default('1.0.0')->after('notes');
            $table->unsignedBigInteger('version_id')->nullable()->after('version');
            $table->boolean('is_latest_version')->default(true)->after('version_id');
            $table->dateTime('version_created_at')->nullable()->after('is_latest_version');
            $table->foreignId('version_created_by')->nullable()->constrained('users')->nullOnDelete()->after('version_created_at');
            $table->text('version_notes')->nullable()->after('version_created_by');

            $table->index(['version']);
            $table->index(['is_latest_version']);
            $table->index(['version_created_at']);
        });

        // Aggiungi campi di versioning a data_breaches
        Schema::table('data_breaches', function (Blueprint $table) {
            $table->string('version')->default('1.0.0')->after('notes');
            $table->unsignedBigInteger('version_id')->nullable()->after('version');
            $table->boolean('is_latest_version')->default(true)->after('version_id');
            $table->dateTime('version_created_at')->nullable()->after('is_latest_version');
            $table->foreignId('version_created_by')->nullable()->constrained('users')->nullOnDelete()->after('version_created_at');
            $table->text('version_notes')->nullable()->after('version_created_by');

            $table->index(['version']);
            $table->index(['is_latest_version']);
            $table->index(['version_created_at']);
        });

        // Aggiungi campi di versioning a data_protection_impact_assessments
        Schema::table('data_protection_impact_assessments', function (Blueprint $table) {
            $table->string('version')->default('1.0.0')->after('notes');
            $table->unsignedBigInteger('version_id')->nullable()->after('version');
            $table->boolean('is_latest_version')->default(true)->after('version_id');
            $table->dateTime('version_created_at')->nullable()->after('is_latest_version');
            $table->foreignId('version_created_by')->nullable()->constrained('users')->nullOnDelete()->after('version_created_at');
            $table->text('version_notes')->nullable()->after('version_created_by');

            $table->index(['version']);
            $table->index(['is_latest_version']);
            $table->index(['version_created_at']);
        });

        // Aggiungi campi di versioning a third_country_transfers
        Schema::table('third_country_transfers', function (Blueprint $table) {
            $table->string('version')->default('1.0.0')->after('notes');
            $table->unsignedBigInteger('version_id')->nullable()->after('version');
            $table->boolean('is_latest_version')->default(true)->after('version_id');
            $table->dateTime('version_created_at')->nullable()->after('is_latest_version');
            $table->foreignId('version_created_by')->nullable()->constrained('users')->nullOnDelete()->after('version_created_at');
            $table->text('version_notes')->nullable()->after('version_created_by');

            $table->index(['version']);
            $table->index(['is_latest_version']);
            $table->index(['version_created_at']);
        });

        // Aggiungi campi di versioning a data_processing_agreements
        Schema::table('data_processing_agreements', function (Blueprint $table) {
            $table->string('version')->default('1.0.0')->after('notes');
            $table->unsignedBigInteger('version_id')->nullable()->after('version');
            $table->boolean('is_latest_version')->default(true)->after('version_id');
            $table->dateTime('version_created_at')->nullable()->after('is_latest_version');
            $table->foreignId('version_created_by')->nullable()->constrained('users')->nullOnDelete()->after('version_created_at');
            $table->text('version_notes')->nullable()->after('version_created_by');

            $table->index(['version']);
            $table->index(['is_latest_version']);
            $table->index(['version_created_at']);
        });

        // Aggiungi campi di versioning a data_subject_rights_requests
        Schema::table('data_subject_rights_requests', function (Blueprint $table) {
            $table->string('version')->default('1.0.0')->after('notes');
            $table->unsignedBigInteger('version_id')->nullable()->after('version');
            $table->boolean('is_latest_version')->default(true)->after('version_id');
            $table->dateTime('version_created_at')->nullable()->after('is_latest_version');
            $table->foreignId('version_created_by')->nullable()->constrained('users')->nullOnDelete()->after('version_created_at');
            $table->text('version_notes')->nullable()->after('version_created_by');

            $table->index(['version']);
            $table->index(['is_latest_version']);
            $table->index(['version_created_at']);
        });

        // Aggiungi campi di versioning alla tabella companies
        Schema::table('companies', function (Blueprint $table) {
            $table->string('register_version')->default('1.0.0')->after('standard_contractual_clauses_used');
            $table->date('register_last_updated')->nullable()->after('register_version');
            $table->foreignId('register_last_updated_by')->nullable()->constrained('users')->nullOnDelete()->after('register_last_updated');
            $table->text('register_update_notes')->nullable()->after('register_last_updated_by');

            $table->index(['register_version']);
            $table->index(['register_last_updated']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rimuovi campi di versioning da data_processing_activities
        Schema::table('data_processing_activities', function (Blueprint $table) {
            $table->dropIndex(['version']);
            $table->dropIndex(['is_latest_version']);
            $table->dropIndex(['version_created_at']);

            $table->dropColumn([
                'version', 'version_id', 'is_latest_version',
                'version_created_at', 'version_created_by', 'version_notes'
            ]);
        });

        // Rimuovi campi di versioning da data_breaches
        Schema::table('data_breaches', function (Blueprint $table) {
            $table->dropIndex(['version']);
            $table->dropIndex(['is_latest_version']);
            $table->dropIndex(['version_created_at']);

            $table->dropColumn([
                'version', 'version_id', 'is_latest_version',
                'version_created_at', 'version_created_by', 'version_notes'
            ]);
        });

        // Rimuovi campi di versioning da data_protection_impact_assessments
        Schema::table('data_protection_impact_assessments', function (Blueprint $table) {
            $table->dropIndex(['version']);
            $table->dropIndex(['is_latest_version']);
            $table->dropIndex(['version_created_at']);

            $table->dropColumn([
                'version', 'version_id', 'is_latest_version',
                'version_created_at', 'version_created_by', 'version_notes'
            ]);
        });

        // Rimuovi campi di versioning da third_country_transfers
        Schema::table('third_country_transfers', function (Blueprint $table) {
            $table->dropIndex(['version']);
            $table->dropIndex(['is_latest_version']);
            $table->dropIndex(['version_created_at']);

            $table->dropColumn([
                'version', 'version_id', 'is_latest_version',
                'version_created_at', 'version_created_by', 'version_notes'
            ]);
        });

        // Rimuovi campi di versioning da data_processing_agreements
        Schema::table('data_processing_agreements', function (Blueprint $table) {
            $table->dropIndex(['version']);
            $table->dropIndex(['is_latest_version']);
            $table->dropIndex(['version_created_at']);

            $table->dropColumn([
                'version', 'version_id', 'is_latest_version',
                'version_created_at', 'version_created_by', 'version_notes'
            ]);
        });

        // Rimuovi campi di versioning da data_subject_rights_requests
        Schema::table('data_subject_rights_requests', function (Blueprint $table) {
            $table->dropIndex(['version']);
            $table->dropIndex(['is_latest_version']);
            $table->dropIndex(['version_created_at']);

            $table->dropColumn([
                'version', 'version_id', 'is_latest_version',
                'version_created_at', 'version_created_by', 'version_notes'
            ]);
        });

        // Rimuovi campi di versioning da companies
        Schema::table('companies', function (Blueprint $table) {
            $table->dropIndex(['register_version']);
            $table->dropIndex(['register_last_updated']);

            $table->dropColumn([
                'register_version', 'register_last_updated',
                'register_last_updated_by', 'register_update_notes'
            ]);
        });
    }
};
