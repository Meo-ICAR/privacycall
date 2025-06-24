<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Company;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'company_id' => Company::inRandomOrder()->first()?->id ?? Company::factory(),
            'customer_number' => $this->faker->unique()->numerify('CUST###'),
            'address_line_1' => $this->faker->streetAddress(),
        ];
    }
}
