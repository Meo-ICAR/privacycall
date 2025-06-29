<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Company;
use App\Models\ProcessingRegisterVersion;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProcessingRegC>
 */
class ProcessingRegCFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::inRandomOrder()->first()?->id ?? Company::factory(),
            'processing_register_version_id' => ProcessingRegisterVersion::inRandomOrder()->first()?->id ?? ProcessingRegisterVersion::factory(),
            'change_type' => fake()->randomElement(['created', 'updated', 'deleted', 'status_changed']),
            'entity_type' => fake()->randomElement(['data_processing_activity', 'data_breach', 'dpia', 'third_country_transfer', 'data_processing_agreement', 'data_subject_rights_request', 'company']),
            'entity_id' => fake()->randomNumber(5, true),
            'entity_name' => fake()->words(2, true),
            'old_values' => [fake()->word(), fake()->word()],
            'new_values' => [fake()->word(), fake()->word()],
            'change_description' => fake()->sentence(),
            'change_reason' => fake()->optional()->sentence(),
            'impact_level' => fake()->randomElement(['low', 'medium', 'high', 'critical']),
            'requires_review' => fake()->boolean(),
            'requires_approval' => fake()->boolean(),
            'changed_by' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'reviewed_by' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'reviewed_at' => fake()->optional()->dateTime(),
            'review_notes' => fake()->optional()->sentence(),
            'review_status' => fake()->randomElement(['pending', 'approved', 'rejected']),
            'is_approved' => fake()->boolean(),
            'approved_at' => fake()->optional()->dateTime(),
            'approved_by' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'approval_notes' => fake()->optional()->sentence(),
        ];
    }
}
