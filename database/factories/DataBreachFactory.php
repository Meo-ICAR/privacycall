<?php

namespace Database\Factories;

use App\Models\DataBreach;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class DataBreachFactory extends Factory
{
    protected $model = DataBreach::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::inRandomOrder()->first()?->id ?? Company::factory(),
            'breach_number' => 'BR-' . date('Y') . '-' . str_pad($this->faker->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'breach_type' => $this->faker->randomElement(['unauthorized_access', 'data_loss', 'system_failure', 'human_error']),
            'severity' => $this->faker->randomElement(['low', 'medium', 'high', 'critical']),
            'status' => $this->faker->randomElement(['detected', 'investigating', 'contained', 'resolved', 'closed']),
            'detection_date' => $this->faker->dateTimeBetween('-2 years', 'now'),
            'breach_description' => $this->faker->sentence(10),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
