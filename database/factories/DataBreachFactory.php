<?php

namespace Database\Factories;

use App\Models\DataBreach;
use Illuminate\Database\Eloquent\Factories\Factory;

class DataBreachFactory extends Factory
{
    protected $model = DataBreach::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'detection_date' => $this->faker->date(),
            'status' => $this->faker->randomElement(['detected', 'investigating', 'resolved']),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
