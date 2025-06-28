<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Company;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Mandator>
 */
class MandatorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'position' => $this->faker->jobTitle(),
            'department' => $this->faker->randomElement(['Legal', 'Compliance', 'IT', 'HR', 'Finance']),
            'logo_url' => null,
            'original_id' => null,
            'disclosure_subscriptions' => $this->faker->randomElements([
                'gdpr_updates',
                'data_breach_notifications',
                'privacy_policy_changes',
                'consent_management',
                'security_updates',
                'employee_data_processing',
                'third_party_disclosures',
                'data_retention_changes'
            ], $this->faker->numberBetween(0, 4)),
            'last_disclosure_date' => $this->faker->optional()->dateTimeBetween('-1 year', 'now'),
            'is_active' => $this->faker->boolean(80), // 80% chance of being active
            'notes' => $this->faker->optional()->paragraph(),
            'email_notifications' => $this->faker->boolean(90), // 90% chance of email notifications
            'sms_notifications' => $this->faker->boolean(30), // 30% chance of SMS notifications
            'preferred_contact_method' => $this->faker->randomElement(['email', 'phone', 'sms']),
        ];
    }

    /**
     * Indicate that the mandator is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the mandator is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the mandator is a clone.
     */
    public function clone(int $originalId): static
    {
        return $this->state(fn (array $attributes) => [
            'original_id' => $originalId,
        ]);
    }

    /**
     * Indicate that the mandator has specific disclosure subscriptions.
     */
    public function withSubscriptions(array $subscriptions): static
    {
        return $this->state(fn (array $attributes) => [
            'disclosure_subscriptions' => $subscriptions,
        ]);
    }
}
