<?php

namespace Database\Factories;

use App\Models\DataProcessingActivity;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Company;

class DataProcessingActivityFactory extends Factory
{
    protected $model = DataProcessingActivity::class;

    public function definition(): array
    {
        return [
            'activity_name' => $this->faker->words(3, true),
            'description' => $this->faker->paragraph(),
            'purpose' => $this->faker->sentence(),
            'legal_basis' => $this->faker->randomElement(['consent', 'contract', 'legal obligation', 'vital interests', 'public task', 'legitimate interests']),
            'company_id' => Company::inRandomOrder()->first()?->id ?? Company::factory(),
            'data_controller_name' => $this->faker->company(),
            'data_controller_contact_email' => $this->faker->companyEmail(),
            'data_controller_contact_phone' => $this->faker->phoneNumber(),
            'dpo_name' => $this->faker->name(),
            'dpo_email' => $this->faker->email(),
            'dpo_phone' => $this->faker->phoneNumber(),
            'processing_method' => $this->faker->randomElement(['automated', 'manual', 'hybrid']),
            'data_sources' => $this->faker->sentence(),
            'data_flows' => $this->faker->sentence(),
            'data_storage_locations' => json_encode([$this->faker->city(), $this->faker->country()]),
            'risk_assessment_date' => $this->faker->date(),
            'risk_assessment_methodology' => $this->faker->sentence(),
            'risk_mitigation_measures' => $this->faker->sentence(),
            'supervisory_authority' => $this->faker->company(),
            'supervisory_authority_contact' => $this->faker->email(),
            'compliance_status' => $this->faker->randomElement(['compliant', 'non_compliant', 'under_review']),
            'last_compliance_review_date' => $this->faker->date(),
            'next_compliance_review_date' => $this->faker->date(),
            'supporting_documents' => json_encode([$this->faker->uuid()]),
            'privacy_notice_version' => 'v' . $this->faker->randomDigitNotNull(),
            'privacy_notice_date' => $this->faker->date(),
            'processing_volume' => $this->faker->randomNumber(3) . ' records',
            'processing_frequency' => $this->faker->randomElement(['daily', 'weekly', 'monthly', 'yearly']),
            'last_activity_review_date' => $this->faker->date(),
            'parent_activity_id' => null,
            'related_activities' => json_encode([]),
        ];
    }
}
