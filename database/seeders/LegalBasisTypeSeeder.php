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
        $legalBasisTypes = [
            [
                'name' => 'consent',
                'description' => 'The data subject has given clear consent for the processing of their personal data for a specific purpose.',
                'gdpr_article' => 'Article 6(1)(a)',
                'requirements' => 'Consent must be freely given, specific, informed, and unambiguous. It must be as easy to withdraw as to give.',
                'examples' => 'Newsletter subscriptions, marketing communications, cookie consent, research participation',
                'sort_order' => 1,
            ],
            [
                'name' => 'contract',
                'description' => 'Processing is necessary for the performance of a contract to which the data subject is party or in order to take steps at the request of the data subject prior to entering into a contract.',
                'gdpr_article' => 'Article 6(1)(b)',
                'requirements' => 'The processing must be necessary for the performance of the contract or for pre-contractual measures.',
                'examples' => 'Employment contracts, service agreements, purchase contracts, insurance policies',
                'sort_order' => 2,
            ],
            [
                'name' => 'legal_obligation',
                'description' => 'Processing is necessary for compliance with a legal obligation to which the controller is subject.',
                'gdpr_article' => 'Article 6(1)(c)',
                'requirements' => 'The legal obligation must be laid down by EU or Member State law.',
                'examples' => 'Tax reporting, regulatory compliance, court orders, statutory reporting',
                'sort_order' => 3,
            ],
            [
                'name' => 'vital_interests',
                'description' => 'Processing is necessary in order to protect the vital interests of the data subject or of another natural person.',
                'gdpr_article' => 'Article 6(1)(d)',
                'requirements' => 'Processing must be necessary to protect someone\'s life.',
                'examples' => 'Emergency medical treatment, disaster response, life-threatening situations',
                'sort_order' => 4,
            ],
            [
                'name' => 'public_task',
                'description' => 'Processing is necessary for the performance of a task carried out in the public interest or in the exercise of official authority vested in the controller.',
                'gdpr_article' => 'Article 6(1)(e)',
                'requirements' => 'The task must be laid down by EU or Member State law.',
                'examples' => 'Public administration, law enforcement, public health, education',
                'sort_order' => 5,
            ],
            [
                'name' => 'legitimate_interests',
                'description' => 'Processing is necessary for the purposes of the legitimate interests pursued by the controller or by a third party, except where such interests are overridden by the interests or fundamental rights and freedoms of the data subject.',
                'gdpr_article' => 'Article 6(1)(f)',
                'requirements' => 'Must conduct a legitimate interest assessment (LIA) and ensure interests do not override data subject rights.',
                'examples' => 'Fraud prevention, network security, direct marketing (with opt-out), business development',
                'sort_order' => 6,
            ],
        ];

        foreach ($legalBasisTypes as $type) {
            LegalBasisType::updateOrCreate(
                ['name' => $type['name']],
                $type
            );
        }
    }
}
