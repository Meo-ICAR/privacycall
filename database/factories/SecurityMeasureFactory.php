<?php

namespace Database\Factories;

use App\Models\SecurityMeasure;
use Illuminate\Database\Eloquent\Factories\Factory;

class SecurityMeasureFactory extends Factory
{
    protected $model = SecurityMeasure::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(['encryption', 'access_control', 'pseudonymization', 'backup', 'firewall']),
            'description' => $this->faker->sentence(),
        ];
    }
}
