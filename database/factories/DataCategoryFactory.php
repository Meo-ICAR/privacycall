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
            'is_active' => $this->faker->boolean(90),
        ];
    }
}
