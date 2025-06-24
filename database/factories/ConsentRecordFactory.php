<?php

namespace Database\Factories;

use App\Models\ConsentRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConsentRecordFactory extends Factory
{
    protected $model = ConsentRecord::class;

    public function definition(): array
    {
        return [
            'customer_id' => null, // Set in seeder
            'company_id' => null, // Set in seeder
            'consent_given' => $this->faker->boolean(90),
            'consent_date' => $this->faker->dateTimeBetween('-2 years', 'now'),
            'purpose' => $this->faker->sentence(),
        ];
    }
}
