<?php

namespace Database\Factories;

use App\Models\CompanyEmail;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EmailDocument>
 */
class EmailDocumentFactory extends Factory
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
            'filename' => $this->faker->unique()->lexify('document_??????.pdf'),
            'original_name' => $this->faker->word() . '.pdf',
            'mime_type' => 'application/pdf',
            'size' => $this->faker->numberBetween(1000, 1000000),
            'storage_path' => '/email-documents/' . $this->faker->unique()->lexify('file_??????.pdf'),
        ];
    }
}
