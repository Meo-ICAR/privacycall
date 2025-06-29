<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SecurityMeasure;

class SecurityMeasureSeeder extends Seeder
{
    public function run(): void
    {
        $measures = [
            [
                'name' => 'encryption',
                'description' => 'Protects data by converting it into a secure format.',
                'category' => 'technical',
                'effectiveness_level' => 'high',
            ],
            [
                'name' => 'access_control',
                'description' => 'Restricts access to authorized personnel only.',
                'category' => 'organizational',
                'effectiveness_level' => 'high',
            ],
            [
                'name' => 'pseudonymization',
                'description' => 'Replaces identifying fields with pseudonyms.',
                'category' => 'technical',
                'effectiveness_level' => 'medium',
            ],
            [
                'name' => 'backup',
                'description' => 'Regularly copies data to prevent loss.',
                'category' => 'administrative',
                'effectiveness_level' => 'high',
            ],
            [
                'name' => 'firewall',
                'description' => 'Monitors and controls incoming and outgoing network traffic.',
                'category' => 'technical',
                'effectiveness_level' => 'very_high',
            ],
        ];
        foreach ($measures as $measure) {
            SecurityMeasure::updateOrCreate(
                ['name' => $measure['name']],
                [
                    'description' => $measure['description'],
                    'category' => $measure['category'],
                    'effectiveness_level' => $measure['effectiveness_level'],
                    'is_active' => true,
                ]
            );
        }
    }
}
