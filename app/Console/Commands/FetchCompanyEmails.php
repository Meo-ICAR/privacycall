<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Services\EmailIntegrationService;
use Illuminate\Console\Command;

class FetchCompanyEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails:fetch {--company-id= : Fetch emails for specific company} {--all : Fetch emails for all companies}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch emails for companies with data controller contacts';

    protected $emailService;

    public function __construct(EmailIntegrationService $emailService)
    {
        parent::__construct();
        $this->emailService = $emailService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $companyId = $this->option('company-id');
        $fetchAll = $this->option('all');

        if ($companyId) {
            $company = Company::find($companyId);
            if (!$company) {
                $this->error("Company with ID {$companyId} not found.");
                return 1;
            }

            if (!$company->data_controller_contact) {
                $this->error("Company {$company->name} does not have a data controller contact email.");
                return 1;
            }

            $this->fetchEmailsForCompany($company);
        } elseif ($fetchAll) {
            $companies = Company::whereNotNull('data_controller_contact')
                ->where('data_controller_contact', '!=', '')
                ->get();

            if ($companies->isEmpty()) {
                $this->info('No companies with data controller contacts found.');
                return 0;
            }

            $this->info("Found {$companies->count()} companies with data controller contacts.");

            $bar = $this->output->createProgressBar($companies->count());
            $bar->start();

            foreach ($companies as $company) {
                $this->fetchEmailsForCompany($company, false);
                $bar->advance();
            }

            $bar->finish();
            $this->newLine();
            $this->info('Email fetching completed for all companies.');
        } else {
            $this->error('Please specify either --company-id or --all option.');
            $this->info('Usage examples:');
            $this->info('  php artisan emails:fetch --company-id=1');
            $this->info('  php artisan emails:fetch --all');
            return 1;
        }

        return 0;
    }

    protected function fetchEmailsForCompany(Company $company, bool $showOutput = true)
    {
        if ($showOutput) {
            $this->info("Fetching emails for company: {$company->name}");
        }

        try {
            $result = $this->emailService->fetchEmailsForCompany($company);

            if ($result['success']) {
                if ($showOutput) {
                    $this->info("Successfully processed {$result['processed']} emails for {$company->name}");
                }
            } else {
                if ($showOutput) {
                    $this->error("Error fetching emails for {$company->name}: {$result['error']}");
                }
            }
        } catch (\Exception $e) {
            if ($showOutput) {
                $this->error("Exception while fetching emails for {$company->name}: " . $e->getMessage());
            }
        }
    }
}
