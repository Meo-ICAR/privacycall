<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Company;
use App\Models\User;
use App\Models\Supplier;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AuditRequest>
 */
class AuditRequestFactory extends Factory
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
            'supplier_id' => Supplier::inRandomOrder()->first()?->id ?? Supplier::factory(),
            'audit_type' => fake()->randomElement(['compliance', 'security', 'gdpr', 'financial', 'operational']),
            'audit_scope' => fake()->randomElement(['full', 'partial', 'specific_area']),
            'status' => fake()->randomElement(['pending', 'in_progress', 'completed', 'cancelled']),
            'priority' => fake()->randomElement(['low', 'normal', 'high', 'urgent']),
            'subject' => fake()->sentence(),
            'message' => fake()->paragraph(),
            'requested_documents' => [fake()->word(), fake()->word()],
            'received_documents' => [fake()->word(), fake()->word()],
            'requested_deadline' => fake()->date(),
            'scheduled_date' => fake()->optional()->date(),
            'scheduled_time' => fake()->optional()->time('H:i'),
            'meeting_type' => fake()->optional()->randomElement(['call', 'visit', 'video_conference']),
            'meeting_link' => fake()->optional()->url(),
            'meeting_location' => fake()->optional()->address(),
            'notes' => fake()->optional()->sentence(),
            'follow_up_dates' => [fake()->date(), fake()->date()],
            'last_follow_up' => fake()->optional()->dateTime(),
            'completed_at' => fake()->optional()->dateTime(),
            'compliance_score' => fake()->numberBetween(0, 100),
            'risk_level' => fake()->randomElement(['low', 'medium', 'high', 'critical']),
            'next_audit_date' => fake()->optional()->date(),
            'audit_findings' => [fake()->sentence(), fake()->sentence()],
            'corrective_actions' => [fake()->sentence(), fake()->sentence()],
            'audit_cost' => fake()->randomFloat(2, 100, 10000),
            'audit_duration_hours' => fake()->randomFloat(2, 1, 40),
            'auditor_assigned' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'supplier_response_deadline' => fake()->optional()->date(),
            'supplier_response_received' => fake()->boolean(),
            'audit_report_url' => fake()->optional()->url(),
            'certification_status' => fake()->optional()->randomElement(['pending', 'granted', 'expired']),
            'certification_expiry_date' => fake()->optional()->date(),
        ];
    }
}
