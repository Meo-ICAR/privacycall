<?php

namespace Database\Factories;

use App\Models\DataProcessingAgreement;
use Illuminate\Database\Eloquent\Factories\Factory;

class DataProcessingAgreementFactory extends Factory
{
    protected $model = DataProcessingAgreement::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company . ' Agreement',
            'agreement_date' => $this->faker->date(),
            'agreement_status' => $this->faker->randomElement(['active', 'expired', 'terminated']),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
