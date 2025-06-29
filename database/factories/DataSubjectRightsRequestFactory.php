<?php

namespace Database\Factories;

use App\Models\DataSubjectRightsRequest;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Company;

class DataSubjectRightsRequestFactory extends Factory
{
    protected $model = DataSubjectRightsRequest::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::inRandomOrder()->first()?->id ?? Company::factory(),
            'request_type' => $this->faker->randomElement(['access', 'rectification', 'erasure', 'portability', 'restriction', 'objection', 'automated_decision_making']),
            'requester_type' => $this->faker->randomElement(['customer', 'employee', 'supplier', 'other']),
            'request_number' => 'DSRR-' . date('Y') . '-' . str_pad($this->faker->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'request_date' => $this->faker->dateTimeBetween('-2 years', 'now'),
            'response_deadline' => $this->faker->dateTimeBetween('now', '+1 month'),
            'status' => $this->faker->randomElement(['received', 'processing', 'completed', 'rejected', 'extended', 'cancelled']),
            'request_description' => $this->faker->sentence(10),
        ];
    }
}
