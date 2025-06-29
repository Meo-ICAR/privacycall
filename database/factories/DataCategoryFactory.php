<?php

namespace Database\Factories;

use App\Models\DataCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class DataCategoryFactory extends Factory
{
    protected $model = DataCategory::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(['personal_data', 'sensitive_data', 'special_categories']),
            'description' => $this->faker->sentence(8),
            'sensitivity_level' => $this->faker->randomElement(['low', 'medium', 'high', 'very_high']),
            'is_active' => $this->faker->boolean(90),
        ];
    }
}
