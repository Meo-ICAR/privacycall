<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Customer;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Training>
 */
class TrainingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->optional()->paragraph(),
            'type' => $this->faker->randomElement(['online', 'in_person']),
            'date' => $this->faker->optional()->date(),
            'duration' => $this->faker->optional()->randomElement(['1 hour', '2 hours', 'Half day', 'Full day']),
            'provider' => $this->faker->optional()->company(),
            'location' => $this->faker->optional()->city(),
            // Always assign a valid customer_id by default
            'customer_id' => Customer::inRandomOrder()->first()?->id ?? Customer::factory(),
        ];
    }
}
