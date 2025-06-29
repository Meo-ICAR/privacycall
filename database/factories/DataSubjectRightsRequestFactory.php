<?php

namespace Database\Factories;

use App\Models\DataSubjectRightsRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

class DataSubjectRightsRequestFactory extends Factory
{
    protected $model = DataSubjectRightsRequest::class;

    public function definition(): array
    {
        return [
            'data_subject_name' => $this->faker->name(),
            'right_type' => $this->faker->randomElement(['access', 'rectification', 'erasure', 'portability']),
            'status' => $this->faker->randomElement(['received', 'processing', 'completed']),
            'description' => $this->faker->optional()->sentence(),
        ];
    }
}
