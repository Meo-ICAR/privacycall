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
            'email' => $this->faker->firstName() . '.' . $this->faker->lastName() . '_' . uniqid() . '@example.com',
            'phone' => $this->faker->phoneNumber(),
            'company_id' => Company::inRandomOrder()->first()?->id ?? Company::factory(),
            'customer_number' => 'CUST' . $this->faker->numerify('###') . '_' . uniqid(),
            'address_line_1' => $this->faker->streetAddress(),
        ];
    }
}
