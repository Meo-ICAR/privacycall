<?php

namespace Database\Factories;

use App\Models\Subsupplier;
use App\Models\Supplier;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubsupplierFactory extends Factory
{
    protected $model = Subsupplier::class;

    public function definition(): array
    {
        return [
            'supplier_id' => Supplier::factory(),
            'company_id' => Company::factory(),
            'service_description' => $this->faker->sentence(6),
        ];
    }
}
