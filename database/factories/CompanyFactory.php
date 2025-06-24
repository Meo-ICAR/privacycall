<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Company::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $companyTypes = ['employer', 'customer', 'supplier', 'partner'];
        $sizes = ['small', 'medium', 'large'];
        $industries = [
            'Technology', 'Healthcare', 'Finance', 'Retail', 'Manufacturing',
            'Education', 'Transportation', 'Construction', 'Real Estate', 'Consulting'
        ];

        return [
            'name' => fake()->company(),
            'legal_name' => fake()->optional()->company(),
            'registration_number' => fake()->optional()->regexify('[A-Z]{2}[0-9]{8}'),
            'vat_number' => fake()->optional()->regexify('[A-Z]{2}[0-9]{10}'),
            'address_line_1' => fake()->streetAddress(),
            'address_line_2' => fake()->optional()->secondaryAddress(),
            'city' => fake()->city(),
            'state' => fake()->optional()->state(),
            'postal_code' => fake()->postcode(),
            'country' => fake()->country(),
            'phone' => fake()->optional()->phoneNumber(),
            'email' => fake()->optional()->companyEmail(),
            'website' => fake()->optional()->url(),
            'company_type' => fake()->randomElement($companyTypes),
            'industry' => fake()->randomElement($industries),
            'size' => fake()->randomElement($sizes),

            // GDPR Compliance Fields
            'gdpr_consent_date' => fake()->optional()->dateTimeBetween('-1 year', 'now'),
            'data_retention_period' => fake()->numberBetween(365, 2555), // 1-7 years in days
            'data_processing_purpose' => fake()->optional()->paragraph(),
            'data_controller_contact' => fake()->optional()->email(),
            'data_protection_officer' => fake()->optional()->email(),

            'is_active' => fake()->boolean(80), // 80% chance of being active
            'notes' => fake()->optional()->paragraph(),
        ];
    }

    /**
     * Indicate that the company is an employer.
     */
    public function employer(): static
    {
        return $this->state(fn (array $attributes) => [
            'company_type' => 'employer',
        ]);
    }

    /**
     * Indicate that the company is a customer.
     */
    public function customer(): static
    {
        return $this->state(fn (array $attributes) => [
            'company_type' => 'customer',
        ]);
    }

    /**
     * Indicate that the company is a supplier.
     */
    public function supplier(): static
    {
        return $this->state(fn (array $attributes) => [
            'company_type' => 'supplier',
        ]);
    }

    /**
     * Indicate that the company is a partner.
     */
    public function partner(): static
    {
        return $this->state(fn (array $attributes) => [
            'company_type' => 'partner',
        ]);
    }

    /**
     * Indicate that the company is small.
     */
    public function small(): static
    {
        return $this->state(fn (array $attributes) => [
            'size' => 'small',
        ]);
    }

    /**
     * Indicate that the company is medium.
     */
    public function medium(): static
    {
        return $this->state(fn (array $attributes) => [
            'size' => 'medium',
        ]);
    }

    /**
     * Indicate that the company is large.
     */
    public function large(): static
    {
        return $this->state(fn (array $attributes) => [
            'size' => 'large',
        ]);
    }

    /**
     * Indicate that the company has valid GDPR consent.
     */
    public function withValidGdprConsent(): static
    {
        return $this->state(fn (array $attributes) => [
            'gdpr_consent_date' => fake()->dateTimeBetween('-6 months', 'now'),
        ]);
    }

    /**
     * Indicate that the company has expired GDPR consent.
     */
    public function withExpiredGdprConsent(): static
    {
        return $this->state(fn (array $attributes) => [
            'gdpr_consent_date' => fake()->dateTimeBetween('-2 years', '-1 year'),
        ]);
    }

    /**
     * Indicate that the company is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the company is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
