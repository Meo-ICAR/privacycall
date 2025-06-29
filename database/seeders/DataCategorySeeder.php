<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DataCategory;

class DataCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'personal_data',
                'description' => 'Data relating to an identified or identifiable person.',
                'sensitivity_level' => 'low',
            ],
            [
                'name' => 'sensitive_data',
                'description' => 'Data revealing racial or ethnic origin, political opinions, etc.',
                'sensitivity_level' => 'high',
            ],
            [
                'name' => 'special_categories',
                'description' => 'Special categories of personal data as defined by GDPR.',
                'sensitivity_level' => 'very_high',
            ],
        ];
        foreach ($categories as $cat) {
            DataCategory::updateOrCreate(
                ['name' => $cat['name']],
                [
                    'description' => $cat['description'],
                    'sensitivity_level' => $cat['sensitivity_level'],
                    'is_active' => true,
                ]
            );
        }
    }
}
