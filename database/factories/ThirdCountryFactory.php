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
            'name' => $this->faker->country(),
            'code' => $this->faker->countryCode(),
        ];
    }
}
