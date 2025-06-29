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
        Schema::table('data_processing_activities', function (Blueprint $table) {
            // Informazioni sul responsabile del trattamento
            $table->string('data_controller_name')->nullable()->after('company_id');
            $table->string('data_controller_contact_email')->nullable()->after('data_controller_name');
            $table->string('data_controller_contact_phone')->nullable()->after('data_controller_contact_email');

            // Informazioni sul DPO
            $table->string('dpo_name')->nullable()->after('data_controller_contact_phone');
            $table->string('dpo_email')->nullable()->after('dpo_name');
            $table->string('dpo_phone')->nullable()->after('dpo_email');

            // Dettagli tecnici
            $table->string('processing_method')->nullable()->after('dpo_phone'); // automated, manual, hybrid
            $table->text('data_sources')->nullable()->after('processing_method'); // fonti dei dati
            $table->text('data_flows')->nullable()->after('data_sources'); // flussi di dati
            $table->json('data_storage_locations')->nullable()->after('data_flows'); // luoghi di conservazione

            // Valutazioni e controlli
            $table->date('risk_assessment_date')->nullable()->after('data_storage_locations');
            $table->text('risk_assessment_methodology')->nullable()->after('risk_assessment_date');
            $table->text('risk_mitigation_measures')->nullable()->after('risk_assessment_methodology');

            // Compliance
            $table->string('supervisory_authority')->nullable()->after('risk_mitigation_measures');
            $table->text('supervisory_authority_contact')->nullable()->after('supervisory_authority');
            $table->enum('compliance_status', ['compliant', 'non_compliant', 'under_review'])->default('under_review')->after('supervisory_authority_contact');
            $table->date('last_compliance_review_date')->nullable()->after('compliance_status');
            $table->date('next_compliance_review_date')->nullable()->after('last_compliance_review_date');

            // Documentazione
            $table->json('supporting_documents')->nullable()->after('next_compliance_review_date'); // riferimenti ai documenti
            $table->string('privacy_notice_version')->nullable()->after('supporting_documents');
            $table->date('privacy_notice_date')->nullable()->after('privacy_notice_version');

            // Monitoraggio
            $table->string('processing_volume')->nullable()->after('privacy_notice_date'); // volume di dati processati
            $table->string('processing_frequency')->nullable()->after('processing_volume'); // frequenza di elaborazione
            $table->date('last_activity_review_date')->nullable()->after('processing_frequency');

            // Relazioni
            $table->unsignedBigInteger('parent_activity_id')->nullable()->after('last_activity_review_date'); // per attività correlate
            $table->json('related_activities')->nullable()->after('parent_activity_id'); // array di ID di attività correlate

            // Indici aggiuntivi
            $table->index(['compliance_status']);
            $table->index(['risk_assessment_level']);
            $table->index(['next_compliance_review_date']);
            $table->index(['parent_activity_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_processing_activities', function (Blueprint $table) {
            // Rimuovi gli indici
            $table->dropIndex(['compliance_status']);
            $table->dropIndex(['risk_assessment_level']);
            $table->dropIndex(['next_compliance_review_date']);
            $table->dropIndex(['parent_activity_id']);

            // Rimuovi le colonne
            $table->dropColumn([
                'data_controller_name',
                'data_controller_contact_email',
                'data_controller_contact_phone',
                'dpo_name',
                'dpo_email',
                'dpo_phone',
                'processing_method',
                'data_sources',
                'data_flows',
                'data_storage_locations',
                'risk_assessment_date',
                'risk_assessment_methodology',
                'risk_mitigation_measures',
                'supervisory_authority',
                'supervisory_authority_contact',
                'compliance_status',
                'last_compliance_review_date',
                'next_compliance_review_date',
                'supporting_documents',
                'privacy_notice_version',
                'privacy_notice_date',
                'processing_volume',
                'processing_frequency',
                'last_activity_review_date',
                'parent_activity_id',
                'related_activities'
            ]);
        });
    }
};
