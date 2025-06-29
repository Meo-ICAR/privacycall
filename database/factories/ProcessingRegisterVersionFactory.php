<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Company;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProcessingRegisterVersion>
 */
class ProcessingRegisterVersionFactory extends Factory
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
            'version_number' => fake()->numerify('#.#.#') . '_' . uniqid(),
            'version_name' => fake()->words(3, true),
            'version_description' => fake()->sentence(),
            'status' => fake()->randomElement(['draft', 'active', 'archived', 'superseded']),
            'effective_date' => fake()->optional()->date(),
            'expiry_date' => fake()->optional()->date(),
            'register_data' => [fake()->word(), fake()->word()],
            'activities_summary' => [fake()->sentence(), fake()->sentence()],
            'compliance_summary' => [fake()->sentence(), fake()->sentence()],
            'changes_log' => [fake()->sentence(), fake()->sentence()],
            'created_by' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'approved_by' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'approved_at' => fake()->optional()->dateTime(),
            'approval_notes' => fake()->optional()->sentence(),
            'is_current' => fake()->boolean(),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
