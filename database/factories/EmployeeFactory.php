<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Company;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition(): array
    {
        return [
            'employee_number' => 'EMP' . $this->faker->numerify('###') . '_' . uniqid(),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->firstName() . '.' . $this->faker->lastName() . '_' . uniqid() . '@example.com',
            'phone' => $this->faker->phoneNumber(),
            'position' => $this->faker->jobTitle(),
            'department' => $this->faker->word(),
            'company_id' => Company::inRandomOrder()->first()?->id ?? Company::factory(),
            'hire_date' => $this->faker->dateTimeBetween('-10 years', 'now'),
        ];
    }
}
