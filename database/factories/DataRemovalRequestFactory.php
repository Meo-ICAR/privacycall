<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Mandator;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DataRemovalRequest>
 */
class DataRemovalRequestFactory extends Factory
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
            'customer_id' => Customer::inRandomOrder()->first()?->id ?? Customer::factory(),
            'mandator_id' => Mandator::inRandomOrder()->first()?->id ?? Mandator::factory(),
            'requested_by_user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'request_number' => 'DRR' . fake()->unique()->numerify('2025######'),
            'request_type' => fake()->randomElement(['customer_direct', 'mandator_request', 'legal_obligation', 'system_cleanup']),
            'status' => fake()->randomElement(['pending', 'in_review', 'approved', 'rejected', 'completed', 'cancelled']),
            'priority' => fake()->randomElement(['low', 'medium', 'high', 'urgent']),
            'reason_for_removal' => fake()->sentence(),
            'data_categories_to_remove' => [fake()->word(), fake()->word()],
            'retention_justification' => fake()->sentence(),
            'legal_basis_for_retention' => fake()->sentence(),
            'request_date' => fake()->date(),
            'due_date' => fake()->date(),
            'review_date' => fake()->optional()->date(),
            'completion_date' => fake()->optional()->date(),
            'review_notes' => fake()->optional()->sentence(),
            'rejection_reason' => fake()->optional()->sentence(),
            'completion_notes' => fake()->optional()->sentence(),
            'data_removal_method' => fake()->optional()->word(),
            'identity_verified' => fake()->boolean(),
            'verification_method' => fake()->optional()->word(),
            'verification_notes' => fake()->optional()->sentence(),
            'gdpr_compliant' => fake()->boolean(),
            'compliance_notes' => fake()->optional()->sentence(),
            'notify_third_parties' => fake()->boolean(),
            'third_party_notification_details' => fake()->optional()->sentence(),
            'reviewed_by_user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'completed_by_user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
        ];
    }
}
