<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Training;
use App\Models\Company;
use App\Models\User;
use Carbon\Carbon;

class GdprTrainingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first company and user for testing
        $company = Company::first();
        $user = User::first();

        if (!$company || !$user) {
            $this->command->info('No company or user found. Skipping GDPR training seeder.');
            return;
        }

        $gdprCourses = [
            [
                'title' => 'GDPR Fundamentals for Employees',
                'description' => 'Essential GDPR training covering basic principles, data subject rights, and employee responsibilities. This course provides a solid foundation for all staff members who handle personal data.',
                'duration' => 60, // minutes
                'target_audience' => 'All employees',
                'difficulty_level' => 'beginner',
                'course_type' => 'mandatory',
                'is_active' => true,
                'prerequisites' => 'None',
                'learning_objectives' => [
                    'Understand GDPR basic principles',
                    'Recognize personal data',
                    'Know data subject rights',
                    'Understand employee responsibilities',
                    'Identify data protection best practices'
                ],
                'topics_covered' => [
                    'What is GDPR and why it matters',
                    'Personal data definition and examples',
                    'Data subject rights overview',
                    'Employee responsibilities',
                    'Data protection principles',
                    'Breach reporting procedures'
                ]
            ],
            [
                'title' => 'Data Protection Officer (DPO) Training',
                'description' => 'Comprehensive training for Data Protection Officers covering advanced GDPR concepts, compliance strategies, and organizational responsibilities.',
                'duration' => 240, // 4 hours
                'target_audience' => 'Data Protection Officers, Compliance Managers',
                'difficulty_level' => 'advanced',
                'course_type' => 'specialized',
                'is_active' => true,
                'prerequisites' => 'GDPR Fundamentals or equivalent experience',
                'learning_objectives' => [
                    'Master GDPR compliance requirements',
                    'Develop data protection strategies',
                    'Conduct privacy impact assessments',
                    'Manage data subject requests',
                    'Oversee breach response procedures'
                ],
                'topics_covered' => [
                    'Advanced GDPR concepts',
                    'Privacy Impact Assessments (PIA)',
                    'Data Protection by Design and Default',
                    'Cross-border data transfers',
                    'DPO responsibilities and independence',
                    'Compliance monitoring and reporting',
                    'Audit preparation and management'
                ]
            ],
            [
                'title' => 'Data Subject Rights Management',
                'description' => 'Specialized training on handling data subject requests including right to access, rectification, erasure, and data portability.',
                'duration' => 90, // 1.5 hours
                'target_audience' => 'Customer Service, HR, IT Support',
                'difficulty_level' => 'intermediate',
                'course_type' => 'specialized',
                'is_active' => true,
                'prerequisites' => 'GDPR Fundamentals',
                'learning_objectives' => [
                    'Process data subject requests correctly',
                    'Verify requestor identity',
                    'Respond within legal timeframes',
                    'Handle complex requests',
                    'Document all interactions'
                ],
                'topics_covered' => [
                    'Right to access procedures',
                    'Right to rectification',
                    'Right to erasure (right to be forgotten)',
                    'Right to data portability',
                    'Right to object to processing',
                    'Request verification methods',
                    'Response templates and procedures'
                ]
            ],
            [
                'title' => 'Data Breach Response Training',
                'description' => 'Training on identifying, reporting, and responding to personal data breaches in compliance with GDPR requirements.',
                'duration' => 120, // 2 hours
                'target_audience' => 'IT Security, Management, DPO',
                'difficulty_level' => 'intermediate',
                'course_type' => 'mandatory',
                'is_active' => true,
                'prerequisites' => 'GDPR Fundamentals',
                'learning_objectives' => [
                    'Identify personal data breaches',
                    'Assess breach severity and risks',
                    'Follow reporting procedures',
                    'Implement containment measures',
                    'Communicate with stakeholders'
                ],
                'topics_covered' => [
                    'What constitutes a data breach',
                    'Breach detection and assessment',
                    '72-hour reporting requirement',
                    'Risk assessment methodologies',
                    'Containment and recovery procedures',
                    'Communication strategies',
                    'Documentation requirements'
                ]
            ],
            [
                'title' => 'Marketing and Consent Management',
                'description' => 'Training on lawful basis for processing, consent requirements, and marketing compliance under GDPR.',
                'duration' => 75, // 1.25 hours
                'target_audience' => 'Marketing, Sales, Customer Relations',
                'difficulty_level' => 'intermediate',
                'course_type' => 'specialized',
                'is_active' => true,
                'prerequisites' => 'GDPR Fundamentals',
                'learning_objectives' => [
                    'Understand lawful basis for processing',
                    'Implement proper consent mechanisms',
                    'Manage marketing permissions',
                    'Handle consent withdrawals',
                    'Maintain consent records'
                ],
                'topics_covered' => [
                    'Lawful basis for processing',
                    'Consent requirements and validity',
                    'Marketing consent management',
                    'Consent withdrawal procedures',
                    'Record keeping requirements',
                    'Third-party data sharing',
                    'Email marketing compliance'
                ]
            ],
            [
                'title' => 'IT Security and Data Protection',
                'description' => 'Technical training on implementing data protection measures, encryption, access controls, and secure data handling.',
                'duration' => 180, // 3 hours
                'target_audience' => 'IT Staff, System Administrators, Developers',
                'difficulty_level' => 'intermediate',
                'course_type' => 'technical',
                'is_active' => true,
                'prerequisites' => 'GDPR Fundamentals, Basic IT knowledge',
                'learning_objectives' => [
                    'Implement technical security measures',
                    'Configure access controls',
                    'Apply encryption standards',
                    'Secure data transmission',
                    'Implement data retention policies'
                ],
                'topics_covered' => [
                    'Technical and organizational measures',
                    'Access control implementation',
                    'Encryption standards and methods',
                    'Secure data transmission',
                    'Data backup and recovery',
                    'System security monitoring',
                    'Vendor security assessment'
                ]
            ],
            [
                'title' => 'HR Data Protection Training',
                'description' => 'Specialized training for HR professionals on handling employee data, recruitment processes, and HR-specific GDPR requirements.',
                'duration' => 90, // 1.5 hours
                'target_audience' => 'HR Staff, Recruiters, Line Managers',
                'difficulty_level' => 'intermediate',
                'course_type' => 'specialized',
                'is_active' => true,
                'prerequisites' => 'GDPR Fundamentals',
                'learning_objectives' => [
                    'Handle employee data lawfully',
                    'Manage recruitment data',
                    'Process HR-related requests',
                    'Maintain employee privacy',
                    'Comply with HR-specific requirements'
                ],
                'topics_covered' => [
                    'Employee data processing',
                    'Recruitment data management',
                    'Background check compliance',
                    'Employee monitoring',
                    'HR data retention policies',
                    'Employee rights in HR context',
                    'Workplace privacy considerations'
                ]
            ],
            [
                'title' => 'Vendor and Third-Party Management',
                'description' => 'Training on managing data processors, vendor compliance, and third-party data sharing requirements.',
                'duration' => 105, // 1.75 hours
                'target_audience' => 'Procurement, Legal, Vendor Managers',
                'difficulty_level' => 'intermediate',
                'course_type' => 'specialized',
                'is_active' => true,
                'prerequisites' => 'GDPR Fundamentals',
                'learning_objectives' => [
                    'Assess vendor GDPR compliance',
                    'Draft data processing agreements',
                    'Monitor vendor performance',
                    'Handle vendor breaches',
                    'Manage data sharing arrangements'
                ],
                'topics_covered' => [
                    'Data controller vs processor',
                    'Data processing agreements',
                    'Vendor assessment criteria',
                    'Ongoing vendor monitoring',
                    'Vendor breach management',
                    'Cross-border data transfers',
                    'Sub-processor management'
                ]
            ],
            [
                'title' => 'GDPR Refresher Course',
                'description' => 'Annual refresher training to update employees on GDPR changes, reinforce key concepts, and address common compliance issues.',
                'duration' => 45, // 45 minutes
                'target_audience' => 'All employees (annual requirement)',
                'difficulty_level' => 'beginner',
                'course_type' => 'refresher',
                'is_active' => true,
                'prerequisites' => 'GDPR Fundamentals',
                'learning_objectives' => [
                    'Reinforce GDPR knowledge',
                    'Update on regulatory changes',
                    'Address common compliance issues',
                    'Refresh best practices',
                    'Ensure ongoing compliance'
                ],
                'topics_covered' => [
                    'GDPR updates and changes',
                    'Common compliance mistakes',
                    'Best practices reinforcement',
                    'Case studies and examples',
                    'Q&A and discussion',
                    'Compliance checklist review'
                ]
            ],
            [
                'title' => 'Privacy by Design Workshop',
                'description' => 'Interactive workshop on implementing privacy by design principles in product development and business processes.',
                'duration' => 150, // 2.5 hours
                'target_audience' => 'Product Managers, Developers, Business Analysts',
                'difficulty_level' => 'advanced',
                'course_type' => 'workshop',
                'is_active' => true,
                'prerequisites' => 'GDPR Fundamentals, Data Protection Officer Training',
                'learning_objectives' => [
                    'Apply privacy by design principles',
                    'Conduct privacy impact assessments',
                    'Design privacy-friendly processes',
                    'Integrate privacy into development',
                    'Create privacy-first solutions'
                ],
                'topics_covered' => [
                    'Privacy by design principles',
                    'Privacy impact assessment process',
                    'Data minimization strategies',
                    'User interface privacy considerations',
                    'Privacy-enhancing technologies',
                    'Workshop exercises and case studies'
                ]
            ]
        ];

        foreach ($gdprCourses as $courseData) {
            Training::create([
                'company_id' => $company->id,
                'title' => $courseData['title'],
                'description' => $courseData['description'],
                'type' => 'online',
                'duration' => $courseData['duration'] . ' minutes',
                'provider' => 'Internal GDPR Training',
                'location' => 'Online Platform',
                'date' => Carbon::now()->addDays(rand(7, 90)), // Future training dates
                'is_active' => $courseData['is_active'],
                'notes' => json_encode([
                    'target_audience' => $courseData['target_audience'],
                    'difficulty_level' => $courseData['difficulty_level'],
                    'prerequisites' => $courseData['prerequisites'],
                    'learning_objectives' => $courseData['learning_objectives'],
                    'topics_covered' => $courseData['topics_covered']
                ]),
            ]);
        }

        $this->command->info('GDPR training courses created successfully.');
        $this->command->info('Created ' . count($gdprCourses) . ' GDPR training courses.');
    }
}
