<?php

namespace Database\Factories;

use App\Models\AuthorizationRequest;
use App\Models\Supplier;
use App\Models\Subsupplier;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AuthorizationRequestFactory extends Factory
{
    protected $model = AuthorizationRequest::class;

    public function definition(): array
    {
        return [
            'supplier_id' => Supplier::factory(),
            'subsupplier_id' => Subsupplier::factory(),
            'company_id' => Company::factory(),
            'status' => $this->faker->randomElement(['pending', 'approved', 'denied']),
            'justification' => $this->faker->optional()->sentence(10),
            'review_notes' => $this->faker->optional()->sentence(10),
            'reviewed_at' => $this->faker->optional()->dateTimeThisYear(),
            'reviewed_by' => User::factory(),
        ];
    }
}
