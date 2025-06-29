<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\CompanyEmail;
use App\Models\EmailLog;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EmailReplyAttachment>
 */
class EmailReplyAttachmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_email_id' => CompanyEmail::inRandomOrder()->first()?->id ?? CompanyEmail::factory(),
            'email_log_id' => EmailLog::inRandomOrder()->first()?->id ?? EmailLog::factory(),
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'filename' => $this->faker->unique()->lexify('attachment_??????.pdf'),
            'original_name' => $this->faker->word() . '.pdf',
            'mime_type' => 'application/pdf',
            'size' => $this->faker->numberBetween(1000, 1000000),
            'storage_path' => '/attachments/' . $this->faker->unique()->lexify('file_??????.pdf'),
        ];
    }
}
