<?php

namespace App\Console\Commands;

use App\Models\Training;
use App\Models\Company;
use Illuminate\Console\Command;

class BackfillTrainingCompanyIds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trainings:backfill-company-ids';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfill company_id for existing trainings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to backfill company_id for trainings...');

        // Get trainings without company_id
        $trainings = Training::whereNull('company_id')->get();

        if ($trainings->isEmpty()) {
            $this->info('No trainings found without company_id.');
            return;
        }

        $this->info("Found {$trainings->count()} trainings without company_id.");

        $updatedCount = 0;

        foreach ($trainings as $training) {
            $companyId = null;

            // If training has a customer, use the customer's company
            if ($training->customer_id) {
                $customer = \App\Models\Customer::find($training->customer_id);
                if ($customer && $customer->company_id) {
                    $companyId = $customer->company_id;
                }
            }

            // If no company found via customer, use the first available company
            if (!$companyId) {
                $firstCompany = Company::first();
                if ($firstCompany) {
                    $companyId = $firstCompany->id;
                }
            }

            if ($companyId) {
                $training->update(['company_id' => $companyId]);
                $updatedCount++;
                $this->line("Updated training '{$training->title}' with company_id: {$companyId}");
            } else {
                $this->warn("Could not assign company_id for training '{$training->title}' - no companies available");
            }
        }

        $this->info("Successfully updated {$updatedCount} trainings with company_id.");
    }
}
