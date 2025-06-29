<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class SetupGdprRegister extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gdpr:setup-register {--fresh : Run fresh migrations}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup the complete GDPR register with all required tables and data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Setting up GDPR Register...');

        // Check if we should run fresh migrations
        if ($this->option('fresh')) {
            $this->warn('⚠️  Running fresh migrations will delete all existing data!');
            if (!$this->confirm('Do you want to continue?')) {
                $this->info('Setup cancelled.');
                return 0;
            }

            $this->info('🔄 Running fresh migrations...');
            Artisan::call('migrate:fresh');
        } else {
            $this->info('🔄 Running migrations...');
            Artisan::call('migrate');
        }

        // Run specific GDPR migrations
        $this->info('📋 Running GDPR-specific migrations...');

        $migrations = [
            '2025_01_15_000001_create_data_breaches_table',
            '2025_01_15_000002_create_data_protection_impact_assessments_table',
            '2025_01_15_000003_create_third_country_transfers_table',
            '2025_01_15_000004_create_data_processing_agreements_table',
            '2025_01_15_000005_create_data_subject_rights_requests_table',
            '2025_01_15_000006_create_legal_basis_types_table',
            '2025_01_15_000007_create_data_categories_table',
            '2025_01_15_000008_create_security_measures_table',
            '2025_01_15_000009_create_third_countries_table',
            '2025_01_15_000010_enhance_data_processing_activities_table',
            '2025_01_15_000011_enhance_companies_table_for_gdpr',
            '2025_01_15_000012_create_processing_register_versions_table',
            '2025_01_15_000013_create_processing_register_changes_table',
            '2025_01_15_000014_add_versioning_to_gdpr_tables',
        ];

        foreach ($migrations as $migration) {
            try {
                $this->line("Running migration: {$migration}");
                Artisan::call('migrate', ['--path' => "database/migrations/{$migration}.php"]);
            } catch (\Exception $e) {
                $this->warn("Migration {$migration} may already be run or failed: " . $e->getMessage());
            }
        }

        // Run seeders
        $this->info('🌱 Running GDPR seeders...');

        $seeders = [
            'LegalBasisTypeSeeder',
            'DataCategorySeeder',
            'InitialRegisterVersionSeeder',
        ];

        foreach ($seeders as $seeder) {
            $this->line("Running seeder: {$seeder}");
            try {
                Artisan::call('db:seed', ['--class' => $seeder]);
            } catch (\Exception $e) {
                $this->warn("Seeder {$seeder} failed: " . $e->getMessage());
            }
        }

        // Verify setup
        $this->info('✅ Verifying setup...');

        $tables = [
            'data_processing_activities',
            'data_breaches',
            'data_protection_impact_assessments',
            'third_country_transfers',
            'data_processing_agreements',
            'data_subject_rights_requests',
            'legal_basis_types',
            'data_categories',
            'security_measures',
            'third_countries',
            'processing_register_versions',
            'processing_register_changes',
        ];

        $missingTables = [];
        foreach ($tables as $table) {
            if (!DB::getSchemaBuilder()->hasTable($table)) {
                $missingTables[] = $table;
            }
        }

        if (!empty($missingTables)) {
            $this->error('❌ Missing tables: ' . implode(', ', $missingTables));
            return 1;
        }

        // Check data
        $legalBasisCount = DB::table('legal_basis_types')->count();
        $dataCategoriesCount = DB::table('data_categories')->count();

        $this->info("📊 Setup complete!");
        $this->info("   - Legal basis types: {$legalBasisCount}");
        $this->info("   - Data categories: {$dataCategoriesCount}");
        $this->info("   - All GDPR tables created successfully");

        $this->info('');
        $this->info('🎉 GDPR Register is now ready!');
        $this->info('You can access it at: /gdpr/register');
        $this->info('Dashboard: /gdpr/register/dashboard');
        $this->info('Report: /gdpr/register/report');

        return 0;
    }
}
