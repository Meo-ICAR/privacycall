<?php

namespace Database\Factories;

use App\Models\ThirdCountry;
use Illuminate\Database\Eloquent\Factories\Factory;

class ThirdCountryFactory extends Factory
{
    protected $model = ThirdCountry::class;

    public function definition(): array
    {
        return [
            'country_name' => $this->faker->country(),
            'country_code' => $this->faker->unique()->countryCode(),
        ];
    }
}
