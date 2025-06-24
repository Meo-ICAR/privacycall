<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $roles = ['admin', 'manager', 'employee', 'customer'];

        return [
            'company_id' => Company::factory(),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => fake()->randomElement($roles),
            'is_active' => fake()->boolean(80), // 80% chance of being active

            // GDPR Compliance Fields
            'gdpr_consent_date' => fake()->optional()->dateTimeBetween('-1 year', 'now'),
            'data_processing_consent' => fake()->boolean(70),
            'marketing_consent' => fake()->boolean(50),
            'third_party_sharing_consent' => fake()->boolean(40),
            'data_retention_consent' => fake()->boolean(60),
            'right_to_be_forgotten_requested' => fake()->boolean(5), // Low probability
            'right_to_be_forgotten_date' => fake()->optional()->dateTimeBetween('-1 year', 'now'),
            'data_portability_requested' => fake()->boolean(3), // Low probability
            'data_portability_date' => fake()->optional()->dateTimeBetween('-1 year', 'now'),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the user is a superadmin.
     */
    public function superadmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'superadmin',
            'is_active' => true,
            'gdpr_consent_date' => now(),
            'data_processing_consent' => true,
            'marketing_consent' => true,
            'third_party_sharing_consent' => true,
            'data_retention_consent' => true,
        ]);
    }

    /**
     * Indicate that the user is an admin.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
            'is_active' => true,
            'gdpr_consent_date' => now(),
            'data_processing_consent' => true,
        ]);
    }

    /**
     * Indicate that the user is a manager.
     */
    public function manager(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'manager',
            'is_active' => true,
            'gdpr_consent_date' => now(),
            'data_processing_consent' => true,
        ]);
    }

    /**
     * Indicate that the user is an employee.
     */
    public function employee(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'employee',
            'is_active' => true,
            'gdpr_consent_date' => now(),
            'data_processing_consent' => true,
        ]);
    }

    /**
     * Indicate that the user is a customer.
     */
    public function customer(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'customer',
            'is_active' => true,
            'gdpr_consent_date' => now(),
            'data_processing_consent' => true,
        ]);
    }

    /**
     * Indicate that the user has valid GDPR consent.
     */
    public function withValidGdprConsent(): static
    {
        return $this->state(fn (array $attributes) => [
            'gdpr_consent_date' => fake()->dateTimeBetween('-6 months', 'now'),
            'data_processing_consent' => true,
        ]);
    }

    /**
     * Indicate that the user has expired GDPR consent.
     */
    public function withExpiredGdprConsent(): static
    {
        return $this->state(fn (array $attributes) => [
            'gdpr_consent_date' => fake()->dateTimeBetween('-2 years', '-1 year'),
            'data_processing_consent' => false,
        ]);
    }

    /**
     * Indicate that the user is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the user is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
