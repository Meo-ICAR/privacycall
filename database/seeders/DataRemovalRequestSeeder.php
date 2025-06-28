<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DataRemovalRequest;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Mandator;
use App\Models\User;
use Carbon\Carbon;

class DataRemovalRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first company and user for testing
        $company = Company::first();
        $user = User::first();

        if (!$company || !$user) {
            $this->command->info('No company or user found. Skipping data removal request seeding.');
            return;
        }

        // Get some customers and mandators
        $customers = Customer::where('company_id', $company->id)->take(3)->get();
        $mandators = Mandator::where('company_id', $company->id)->take(2)->get();

        $sampleRequests = [
            [
                'customer_id' => $customers->first()?->id,
                'mandator_id' => null,
                'request_type' => 'customer_direct',
                'priority' => 'high',
                'status' => 'pending',
                'reason_for_removal' => 'Customer requested complete data deletion due to privacy concerns.',
                'data_categories_to_remove' => ['personal_info', 'contact_details', 'transaction_history'],
                'due_date' => Carbon::now()->addDays(7),
                'identity_verified' => true,
                'verification_method' => 'email_verification',
                'notify_third_parties' => true,
                'third_party_notification_details' => 'Notify payment processor and marketing partners.',
            ],
            [
                'customer_id' => null,
                'mandator_id' => $mandators->first()?->id,
                'request_type' => 'mandator_request',
                'priority' => 'urgent',
                'status' => 'in_review',
                'reason_for_removal' => 'Mandator requested data removal for terminated employee.',
                'data_categories_to_remove' => ['all_data'],
                'due_date' => Carbon::now()->addDays(3),
                'identity_verified' => true,
                'verification_method' => 'mandator_confirmation',
                'notify_third_parties' => false,
            ],
            [
                'customer_id' => $customers->skip(1)->first()?->id,
                'mandator_id' => null,
                'request_type' => 'legal_obligation',
                'priority' => 'medium',
                'status' => 'approved',
                'reason_for_removal' => 'Legal requirement to remove data after contract termination.',
                'data_categories_to_remove' => ['financial_data', 'communication_records'],
                'due_date' => Carbon::now()->addDays(14),
                'identity_verified' => false,
                'verification_method' => null,
                'notify_third_parties' => true,
                'third_party_notification_details' => 'Notify legal department and compliance team.',
            ],
            [
                'customer_id' => $customers->skip(2)->first()?->id,
                'mandator_id' => null,
                'request_type' => 'customer_direct',
                'priority' => 'low',
                'status' => 'completed',
                'reason_for_removal' => 'Customer requested marketing data removal only.',
                'data_categories_to_remove' => ['marketing_data', 'preferences'],
                'due_date' => Carbon::now()->subDays(5),
                'identity_verified' => true,
                'verification_method' => 'email_verification',
                'notify_third_parties' => false,
                'completion_date' => Carbon::now()->subDays(3),
                'data_removal_method' => 'permanent_deletion',
                'completion_notes' => 'Marketing data successfully removed from all systems.',
            ],
            [
                'customer_id' => null,
                'mandator_id' => $mandators->skip(1)->first()?->id,
                'request_type' => 'system_cleanup',
                'priority' => 'medium',
                'status' => 'pending',
                'reason_for_removal' => 'System cleanup of inactive customer data older than 5 years.',
                'data_categories_to_remove' => ['all_data'],
                'due_date' => Carbon::now()->addDays(30),
                'identity_verified' => false,
                'verification_method' => null,
                'notify_third_parties' => false,
            ],
        ];

        foreach ($sampleRequests as $requestData) {
            // Skip if no customer or mandator is available
            if (!$requestData['customer_id'] && !$requestData['mandator_id']) {
                continue;
            }

            DataRemovalRequest::create([
                'company_id' => $company->id,
                'customer_id' => $requestData['customer_id'],
                'mandator_id' => $requestData['mandator_id'],
                'requested_by_user_id' => $user->id,
                'request_type' => $requestData['request_type'],
                'priority' => $requestData['priority'],
                'status' => $requestData['status'],
                'reason_for_removal' => $requestData['reason_for_removal'],
                'data_categories_to_remove' => $requestData['data_categories_to_remove'],
                'due_date' => $requestData['due_date'],
                'identity_verified' => $requestData['identity_verified'],
                'verification_method' => $requestData['verification_method'],
                'notify_third_parties' => $requestData['notify_third_parties'],
                'third_party_notification_details' => $requestData['third_party_notification_details'],
                'completion_date' => $requestData['completion_date'] ?? null,
                'data_removal_method' => $requestData['data_removal_method'] ?? null,
                'completion_notes' => $requestData['completion_notes'] ?? null,
                'request_date' => Carbon::now()->subDays(rand(1, 30)),
            ]);
        }

        $this->command->info('Sample data removal requests created successfully.');
    }
}
