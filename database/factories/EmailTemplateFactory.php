<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\EmailTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EmailTemplate>
 */
class EmailTemplateFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EmailTemplate::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = ['general', 'supplier', 'customer', 'employee', 'notification', 'gdpr'];
        $category = $this->faker->randomElement($categories);

        $templates = [
            'general' => [
                'name' => 'General Notification',
                'subject' => 'Important Update from {{company_name}}',
                'body' => "Dear {{recipient_name}},\n\nWe hope this message finds you well. This is a general notification from {{company_name}}.\n\n{{custom_message}}\n\nBest regards,\n{{user_name}}\n{{company_name}}"
            ],
            'supplier' => [
                'name' => 'Supplier Welcome',
                'subject' => 'Welcome to {{company_name}} - Supplier Onboarding',
                'body' => "Dear {{supplier_name}},\n\nWelcome to {{company_name}}! We're excited to have you as our supplier.\n\nYour supplier details:\n- Name: {{supplier_name}}\n- Email: {{supplier_email}}\n- Phone: {{supplier_phone}}\n\nPlease review our supplier guidelines and contact us if you have any questions.\n\nBest regards,\n{{user_name}}\n{{company_name}}"
            ],
            'customer' => [
                'name' => 'Customer Update',
                'subject' => 'Your Account Update - {{company_name}}',
                'body' => "Dear {{recipient_name}},\n\nWe wanted to inform you about an important update to your account with {{company_name}}.\n\n{{custom_message}}\n\nIf you have any questions, please don't hesitate to contact us.\n\nBest regards,\n{{user_name}}\n{{company_name}}"
            ],
            'employee' => [
                'name' => 'Employee Training Reminder',
                'subject' => 'Training Reminder - {{company_name}}',
                'body' => "Dear {{recipient_name}},\n\nThis is a reminder about your upcoming training session.\n\nTraining Details:\n- Date: {{current_date}}\n- Topic: GDPR Compliance\n- Duration: 2 hours\n\nPlease ensure you attend this important session.\n\nBest regards,\n{{user_name}}\n{{company_name}}"
            ],
            'notification' => [
                'name' => 'System Notification',
                'subject' => 'System Update - {{company_name}}',
                'body' => "Hello {{recipient_name}},\n\nThis is an automated notification from {{company_name}}.\n\n{{custom_message}}\n\nThis message was sent on {{current_date}}.\n\nBest regards,\n{{company_name}} System"
            ],
            'gdpr' => [
                'name' => 'GDPR Consent Request',
                'subject' => 'Data Processing Consent - {{company_name}}',
                'body' => "Dear {{recipient_name}},\n\nAs part of our commitment to data protection and GDPR compliance, we need your consent to process your personal data.\n\nPlease review our privacy policy and provide your consent by clicking the link below.\n\n{{custom_message}}\n\nBest regards,\n{{user_name}}\n{{company_name}}\nData Protection Officer"
            ]
        ];

        $template = $templates[$category];

        return [
            'company_id' => Company::inRandomOrder()->first()?->id,
            'name' => $template['name'],
            'subject' => $template['subject'],
            'body' => $template['body'],
            'variables' => [
                'supplier_name' => 'Supplier Name',
                'supplier_email' => 'Supplier Email',
                'supplier_phone' => 'Supplier Phone',
                'company_name' => 'Your Company Name',
                'user_name' => 'Current User Name',
                'current_date' => 'Current Date',
                'custom_message' => 'Custom Message',
                'recipient_name' => 'Recipient Name',
                'recipient_email' => 'Recipient Email',
                'template_name' => 'Template Name'
            ],
            'is_active' => $this->faker->boolean(80), // 80% chance of being active
            'category' => $category,
        ];
    }

    /**
     * Indicate that the template is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the template is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Create a global template (no company assigned).
     */
    public function global(): static
    {
        return $this->state(fn (array $attributes) => [
            'company_id' => null,
        ]);
    }
}
