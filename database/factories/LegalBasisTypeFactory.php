<?php

namespace Database\Factories;

use App\Models\LegalBasisType;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegalBasisTypeFactory extends Factory
{
    protected $model = LegalBasisType::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(['consent', 'contract', 'legal_obligation', 'vital_interests', 'public_task', 'legitimate_interests']),
            'description' => $this->faker->sentence(8),
            'gdpr_article' => 'Art. ' . $this->faker->numberBetween(6, 11),
            'is_active' => $this->faker->boolean(90),
        ];
    }
}
