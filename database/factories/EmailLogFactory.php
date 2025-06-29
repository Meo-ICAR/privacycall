<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Company;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EmailLog>
 */
class EmailLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::inRandomOrder()->first()?->id ?? Company::factory(),
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'recipient_email' => $this->faker->email(),
            'recipient_name' => $this->faker->name(),
            'subject' => $this->faker->sentence(),
            'body' => $this->faker->paragraph(),
            'template_name' => $this->faker->optional()->word(),
            'status' => $this->faker->randomElement(['sent', 'delivered', 'failed']),
            'sent_at' => $this->faker->optional()->dateTime(),
            'delivered_at' => $this->faker->optional()->dateTime(),
            'error_message' => $this->faker->optional()->sentence(),
            'metadata' => ['ip' => $this->faker->ipv4(), 'user_agent' => $this->faker->userAgent()],
        ];
    }
}
