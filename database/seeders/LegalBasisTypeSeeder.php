<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LegalBasisType;

class LegalBasisTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'name' => 'consent',
                'description' => 'The data subject has given consent to the processing of their personal data.',
                'gdpr_article' => 'Art. 6(1)(a)',
            ],
            [
                'name' => 'contract',
                'description' => 'Processing is necessary for the performance of a contract.',
                'gdpr_article' => 'Art. 6(1)(b)',
            ],
            [
                'name' => 'legal_obligation',
                'description' => 'Processing is necessary for compliance with a legal obligation.',
                'gdpr_article' => 'Art. 6(1)(c)',
            ],
            [
                'name' => 'vital_interests',
                'description' => 'Processing is necessary to protect vital interests.',
                'gdpr_article' => 'Art. 6(1)(d)',
            ],
            [
                'name' => 'public_task',
                'description' => 'Processing is necessary for the performance of a task carried out in the public interest.',
                'gdpr_article' => 'Art. 6(1)(e)',
            ],
            [
                'name' => 'legitimate_interests',
                'description' => 'Processing is necessary for the purposes of legitimate interests.',
                'gdpr_article' => 'Art. 6(1)(f)',
            ],
        ];
        foreach ($types as $type) {
            LegalBasisType::updateOrCreate(
                ['name' => $type['name']],
                [
                    'description' => $type['description'],
                    'gdpr_article' => $type['gdpr_article'],
                    'is_active' => true,
                ]
            );
        }
    }
}
