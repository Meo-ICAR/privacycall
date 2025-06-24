<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Supplier;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Document>
 */
class DocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $morphables = [
            Company::class,
            Customer::class,
            Employee::class,
            Supplier::class,
        ];
        $documentableType = $this->faker->randomElement($morphables);
        $documentableId = match ($documentableType) {
            Company::class => Company::factory(),
            Customer::class => Customer::factory(),
            Employee::class => Employee::factory(),
            Supplier::class => Supplier::factory(),
        };
        return [
            'file_name' => $this->faker->lexify('document_????.pdf'),
            'file_path' => $this->faker->filePath(),
            'mime_type' => $this->faker->mimeType(),
            'uploaded_by' => User::factory(),
            'documentable_type' => $documentableType,
            'documentable_id' => $documentableId,
        ];
    }
}
