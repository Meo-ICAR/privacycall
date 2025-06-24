<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition(): array
    {
        return [
            'employee_number' => $this->faker->unique()->numerify('EMP###'),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'position' => $this->faker->jobTitle(),
            'department' => $this->faker->word(),
            'company_id' => null, // Set in seeder
            'hire_date' => $this->faker->dateTimeBetween('-10 years', 'now'),
        ];
    }
}
