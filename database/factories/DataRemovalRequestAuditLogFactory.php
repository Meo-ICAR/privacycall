<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\DataRemovalRequest;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DataRemovalRequestAuditLog>
 */
class DataRemovalRequestAuditLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'data_removal_request_id' => DataRemovalRequest::inRandomOrder()->first()?->id ?? DataRemovalRequest::factory(),
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'action' => fake()->randomElement(['created', 'reviewed', 'approved', 'rejected', 'completed', 'cancelled']),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
