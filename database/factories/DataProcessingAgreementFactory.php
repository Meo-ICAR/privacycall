<?php

namespace Database\Factories;

use App\Models\DataProcessingAgreement;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Company;

class DataProcessingAgreementFactory extends Factory
{
    protected $model = DataProcessingAgreement::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::inRandomOrder()->first()?->id ?? Company::factory(),
            'processor_company_id' => Company::inRandomOrder()->first()?->id ?? Company::factory(),
            'name' => $this->faker->company . ' Agreement',
            'agreement_date' => $this->faker->date(),
            'agreement_status' => $this->faker->randomElement(['active', 'expired', 'terminated']),
            'notes' => $this->faker->optional()->sentence(),
            'agreement_number' => 'DPA-' . $this->faker->unique()->numberBetween(1000, 9999),
            'processing_purposes' => $this->faker->sentence(6),
            'data_categories_processed' => json_encode([$this->faker->randomElement(['personal_data', 'sensitive_data', 'special_categories'])]),
            'data_subjects_affected' => json_encode([$this->faker->randomElement(['employees', 'customers', 'suppliers', 'visitors'])]),
            'processing_duration' => $this->faker->randomElement(['1 year', '2 years', 'indefinite']),
            'security_measures' => $this->faker->sentence(8),
            'data_breach_notification_requirements' => $this->faker->sentence(8),
            'data_subject_rights_assistance' => $this->faker->sentence(8),
            'audit_rights' => $this->faker->sentence(8),
            'data_return_deletion_obligations' => $this->faker->sentence(8),
            'liability_provisions' => $this->faker->sentence(8),
            'termination_conditions' => $this->faker->sentence(8),
        ];
    }
}
