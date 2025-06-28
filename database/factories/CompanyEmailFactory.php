<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\CompanyEmail;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CompanyEmail>
 */
class CompanyEmailFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CompanyEmail::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $company = Company::inRandomOrder()->first() ?? Company::factory()->create();
        $user = User::inRandomOrder()->first();

        $subjects = [
            'GDPR Compliance Inquiry',
            'Data Protection Request',
            'Privacy Policy Update',
            'Right to be Forgotten Request',
            'Data Breach Notification',
            'Consent Management Question',
            'Personal Data Processing Inquiry',
            'Data Subject Rights Request',
            'Privacy Impact Assessment',
            'Data Transfer Agreement',
            'Security Audit Request',
            'Compliance Documentation',
            'Data Processing Agreement',
            'Privacy Notice Update',
            'Data Retention Policy Review'
        ];

        $bodies = [
            'I am writing to inquire about your GDPR compliance procedures and how you handle personal data processing.',
            'We have received a request from a data subject regarding their right to be forgotten. Please advise on the process.',
            'Could you please provide information about your data retention policies and procedures?',
            'We need to update our privacy policy and would like to understand your current data processing activities.',
            'There has been a potential data breach and we need to notify the relevant authorities. Please provide guidance.',
            'We are implementing new consent management procedures and would like to ensure compliance with GDPR requirements.',
            'A customer has requested access to their personal data. What is the process for handling such requests?',
            'We are considering using artificial intelligence for data processing. What are the GDPR implications?',
            'Please provide information about your data protection officer and their contact details.',
            'We need to transfer data to a third country. What safeguards should we implement?',
            'We are conducting a security audit and need information about your data protection measures.',
            'Please provide updated compliance documentation for our records.',
            'We need to review and update our data processing agreement with you.',
            'We have updated our privacy notice and need to inform you of the changes.',
            'Please review our data retention policy and confirm it meets regulatory requirements.'
        ];

        $statuses = ['unread', 'read', 'replied', 'archived'];
        $priorities = ['low', 'normal', 'high', 'urgent'];
        $categories = ['complaint', 'inquiry', 'notification', 'general'];

        $isGdprRelated = $this->faker->boolean(70); // 70% chance of being GDPR-related
        $hasAttachments = $this->faker->boolean(30); // 30% chance of having attachments

        $attachments = null;
        if ($hasAttachments) {
            $attachmentTypes = ['pdf', 'docx', 'xlsx', 'jpg', 'png'];
            $numAttachments = $this->faker->numberBetween(1, 3);
            $attachments = [];

            for ($i = 0; $i < $numAttachments; $i++) {
                $attachments[] = [
                    'name' => $this->faker->words(2, true) . '.' . $this->faker->randomElement($attachmentTypes),
                    'size' => $this->faker->numberBetween(100, 5000),
                    'mime_type' => 'application/' . $this->faker->randomElement($attachmentTypes)
                ];
            }
        }

        return [
            'company_id' => $company->id,
            'user_id' => $user ? $user->id : null,
            'email_id' => 'email_' . $company->id . '_' . $this->faker->unique()->numberBetween(1000, 9999),
            'thread_id' => $this->faker->optional(0.3)->bothify('thread_##??'),
            'from_email' => $this->faker->email(),
            'from_name' => $this->faker->name(),
            'to_email' => $company->data_controller_contact ?? $this->faker->companyEmail(),
            'subject' => $this->faker->randomElement($subjects),
            'body' => $this->faker->randomElement($bodies),
            'body_plain' => $this->faker->randomElement($bodies),
            'attachments' => $attachments,
            'headers' => [
                'X-Mailer' => $this->faker->randomElement(['Outlook', 'Gmail', 'Thunderbird', 'Apple Mail']),
                'Message-ID' => '<' . $this->faker->uuid() . '@example.com>',
                'Date' => now()->toRfc2822String(),
            ],
            'received_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'read_at' => $this->faker->optional(0.6)->dateTimeBetween('-30 days', 'now'),
            'replied_at' => $this->faker->optional(0.3)->dateTimeBetween('-30 days', 'now'),
            'status' => $this->faker->randomElement($statuses),
            'priority' => $this->faker->randomElement($priorities),
            'labels' => $this->faker->randomElements(['INBOX', 'IMPORTANT', 'WORK', 'FOLLOW_UP'], $this->faker->numberBetween(0, 3)),
            'notes' => $this->faker->optional(0.2)->sentence(),
            'is_gdpr_related' => $isGdprRelated,
            'category' => $this->faker->randomElement($categories),
        ];
    }

    /**
     * Indicate that the email is unread.
     */
    public function unread(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'unread',
            'read_at' => null,
        ]);
    }

    /**
     * Indicate that the email is read.
     */
    public function read(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'read',
            'read_at' => now(),
        ]);
    }

    /**
     * Indicate that the email has been replied to.
     */
    public function replied(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'replied',
            'read_at' => now(),
            'replied_at' => now(),
        ]);
    }

    /**
     * Indicate that the email is archived.
     */
    public function archived(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'archived',
        ]);
    }

    /**
     * Indicate that the email is GDPR-related.
     */
    public function gdprRelated(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_gdpr_related' => true,
        ]);
    }

    /**
     * Indicate that the email is urgent.
     */
    public function urgent(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 'urgent',
        ]);
    }

    /**
     * Indicate that the email is high priority.
     */
    public function highPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 'high',
        ]);
    }

    /**
     * Indicate that the email has attachments.
     */
    public function withAttachments(): static
    {
        return $this->state(fn (array $attributes) => [
            'attachments' => [
                [
                    'name' => 'document.pdf',
                    'size' => $this->faker->numberBetween(100, 5000),
                    'mime_type' => 'application/pdf'
                ],
                [
                    'name' => 'data_export.xlsx',
                    'size' => $this->faker->numberBetween(100, 3000),
                    'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                ]
            ],
        ]);
    }
}
