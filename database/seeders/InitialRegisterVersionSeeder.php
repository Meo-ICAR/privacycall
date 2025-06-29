<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProcessingRegisterVersion;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class InitialRegisterVersionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Trova la prima azienda e il primo utente
        $company = Company::first();
        $user = User::first();

        if (!$company || !$user) {
            $this->command->warn('No company or user found. Skipping initial register version creation.');
            return;
        }

        // Crea la versione iniziale del registro
        $version = ProcessingRegisterVersion::create([
            'company_id' => $company->id,
            'version_number' => '1.0.0',
            'version_name' => 'Versione Iniziale',
            'version_description' => 'Versione iniziale del registro dei trattamenti conforme al GDPR',
            'status' => 'active',
            'effective_date' => now(),
            'is_current' => true,
            'created_by' => $user->id,
            'approved_by' => $user->id,
            'approved_at' => now(),
            'approval_notes' => 'Versione iniziale approvata automaticamente',
            'notes' => 'Questa è la versione iniziale del registro dei trattamenti. Tutte le modifiche future verranno tracciate attraverso il sistema di versioning.',
        ]);

        // Genera i dati del registro
        $registerData = $this->generateInitialRegisterData($company);
        $activitiesSummary = $this->generateInitialActivitiesSummary($company);
        $complianceSummary = $this->generateInitialComplianceSummary($company);

        $version->update([
            'register_data' => $registerData,
            'activities_summary' => $activitiesSummary,
            'compliance_summary' => $complianceSummary,
        ]);

        // Aggiorna la versione del registro nell'azienda
        $company->update([
            'register_version' => $version->version_number,
            'register_last_updated' => now(),
            'register_last_updated_by' => $user->id,
            'register_update_notes' => 'Creata versione iniziale del registro',
        ]);

        $this->command->info("✅ Initial register version created: {$version->version_name} ({$version->version_number})");
    }

    /**
     * Generate initial register data.
     */
    private function generateInitialRegisterData(Company $company): array
    {
        return [
            'company_info' => [
                'id' => $company->id,
                'name' => $company->name,
                'legal_form' => $company->legal_form,
                'vat_number' => $company->vat_number,
                'address' => $company->address,
                'city' => $company->city,
                'postal_code' => $company->postal_code,
                'country' => $company->country,
                'phone' => $company->phone,
                'email' => $company->email,
                'website' => $company->website,
                'dpo_name' => $company->dpo_name,
                'dpo_email' => $company->dpo_email,
                'dpo_phone' => $company->dpo_phone,
            ],
            'activities_count' => 0,
            'breaches_count' => 0,
            'dpias_count' => 0,
            'generated_at' => now()->toISOString(),
            'version_info' => [
                'version_number' => '1.0.0',
                'version_name' => 'Versione Iniziale',
                'created_at' => now()->toISOString(),
                'created_by' => 'System',
            ],
        ];
    }

    /**
     * Generate initial activities summary.
     */
    private function generateInitialActivitiesSummary(Company $company): array
    {
        return [
            'total_activities' => 0,
            'active_activities' => 0,
            'by_legal_basis' => [],
            'by_risk_level' => [],
            'by_compliance_status' => [],
            'activities_list' => [],
            'summary' => [
                'message' => 'Nessuna attività di trattamento registrata ancora.',
                'next_steps' => [
                    'Aggiungere le prime attività di trattamento dei dati personali',
                    'Definire le basi legali per ogni attività',
                    'Valutare i rischi associati a ogni attività',
                    'Implementare le misure di sicurezza appropriate',
                ],
            ],
        ];
    }

    /**
     * Generate initial compliance summary.
     */
    private function generateInitialComplianceSummary(Company $company): array
    {
        return [
            'compliance_score' => 0.0,
            'overdue_reviews' => 0,
            'upcoming_reviews' => 0,
            'breaches_this_year' => 0,
            'dpias_required' => 0,
            'dpias_completed' => 0,
            'risk_distribution' => [
                'low' => 0,
                'medium' => 0,
                'high' => 0,
                'very_high' => 0,
            ],
            'compliance_status' => [
                'overall_status' => 'not_started',
                'message' => 'Il registro dei trattamenti è stato creato ma non contiene ancora attività. Inizia ad aggiungere le tue attività di trattamento per migliorare il punteggio di compliance.',
                'recommendations' => [
                    'Inizia registrando le attività di trattamento più critiche',
                    'Assicurati di avere una base legale per ogni attività',
                    'Valuta se sono necessarie DPIA per le attività ad alto rischio',
                    'Implementa un processo di revisione periodica',
                ],
            ],
        ];
    }
}
