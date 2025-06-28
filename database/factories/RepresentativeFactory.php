<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Company;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Representative>
 */
class RepresentativeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $disclosureTypes = [
            'gdpr_updates',
            'data_breach_notifications',
            'privacy_policy_changes',
            'consent_management',
            'security_updates',
            'employee_data_processing',
            'third_party_disclosures',
            'data_retention_changes'
        ];

        return [
            'company_id' => Company::factory(),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'position' => $this->faker->randomElement([
                'Data Protection Officer',
                'Privacy Manager',
                'IT Security Manager',
                'HR Manager',
                'Legal Counsel',
                'Compliance Officer',
                'Chief Privacy Officer',
                'Data Controller'
            ]),
            'department' => $this->faker->randomElement([
                'Legal',
                'Compliance',
                'IT',
                'Human Resources',
                'Risk Management',
                'Operations'
            ]),
            'disclosure_subscriptions' => $this->faker->randomElements($disclosureTypes, $this->faker->numberBetween(1, 4)),
            'last_disclosure_date' => $this->faker->optional()->dateTimeBetween('-1 year', 'now'),
            'is_active' => $this->faker->boolean(80), // 80% chance of being active
            'notes' => $this->faker->optional()->sentence(),
            'email_notifications' => $this->faker->boolean(90), // 90% chance of email notifications
            'sms_notifications' => $this->faker->boolean(30), // 30% chance of SMS notifications
            'preferred_contact_method' => $this->faker->randomElement(['email', 'phone', 'sms']),
        ];
    }

    /**
     * Indicate that the representative is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the representative is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the representative has specific disclosure subscriptions.
     */
    public function withDisclosureSubscriptions(array $subscriptions): static
    {
        return $this->state(fn (array $attributes) => [
            'disclosure_subscriptions' => $subscriptions,
        ]);
    }

    /**
     * Indicate that the representative has no disclosure subscriptions.
     */
    public function withoutDisclosureSubscriptions(): static
    {
        return $this->state(fn (array $attributes) => [
            'disclosure_subscriptions' => [],
        ]);
    }
}
