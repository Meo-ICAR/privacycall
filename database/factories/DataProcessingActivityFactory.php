<?php

namespace Database\Factories;

use App\Models\DataProcessingActivity;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Company;

class DataProcessingActivityFactory extends Factory
{
    protected $model = DataProcessingActivity::class;

    public function definition(): array
    {
        return [
            'activity_name' => $this->faker->words(3, true),
            'description' => $this->faker->paragraph(),
            'purpose' => $this->faker->sentence(),
            'legal_basis' => $this->faker->randomElement(['consent', 'contract', 'legal obligation', 'vital interests', 'public task', 'legitimate interests']),
            'company_id' => Company::inRandomOrder()->first()?->id ?? Company::factory(),
        ];
    }
}
