<?php

namespace Database\Seeders;

use App\Models\Case_Model;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class CaseSeeder extends Seeder
{
    public function run()
    {
        // Create a test user if doesn't exist
        $user = User::firstOrCreate(
            ['email' => 'lawyer@example.com'],
            [
                'name' => 'John Lawyer',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $cases = [
            [
                'case_number' => 'CASE-2024-0001',
                'case_title' => 'Smith vs. Johnson Property Dispute',
                'first_party_name' => 'Robert Smith',
                'first_party_contact' => '+1234567890',
                'first_party_cnic' => '12345-6789012-3',
                'first_party_address' => '123 Main Street, New York, NY',
                'second_party_name' => 'Michael Johnson',
                'second_party_contact' => '+0987654321',
                'second_party_cnic' => '54321-1098765-4',
                'second_party_address' => '456 Oak Avenue, New York, NY',
                'case_type' => 'civil',
                'court_name' => 'Supreme Court',
                'case_status' => 'open',
                'filing_date' => Carbon::now()->subDays(15),
                'hearing_date' => Carbon::now()->addDays(30),
                'case_notes' => 'Property boundary dispute between neighbors. Both parties have submitted evidence.',
                'case_details' => [
                    'court_type' => 'Civil Court',
                    'dispute_details' => 'Property boundary and ownership dispute'
                ],
                'documents' => [
                    [
                        'name' => 'property_deed.pdf',
                        'path' => 'case-documents/property_deed_123.pdf',
                        'size' => 2048576,
                        'type' => 'application/pdf'
                    ]
                ],
                'user_id' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'case_number' => 'CASE-2024-0002',
                'case_title' => 'State vs. Anderson Criminal Case',
                'first_party_name' => 'State of New York',
                'first_party_contact' => '+1112223333',
                'first_party_cnic' => null,
                'first_party_address' => 'District Attorney Office, New York',
                'second_party_name' => 'David Anderson',
                'second_party_contact' => '+4445556666',
                'second_party_cnic' => '98765-4321098-7',
                'second_party_address' => '789 Pine Road, Brooklyn, NY',
                'case_type' => 'criminal',
                'court_name' => 'High Court',
                'case_status' => 'pending',
                'filing_date' => Carbon::now()->subDays(10),
                'hearing_date' => Carbon::now()->addDays(15),
                'case_notes' => 'Criminal case involving theft and burglary charges. Defendant has legal representation.',
                'case_details' => [
                    'fir_number' => 'FIR-2024-0456',
                    'police_station_name' => 'NYPD Central Precinct'
                ],
                'documents' => [
                    [
                        'name' => 'fir_document.pdf',
                        'path' => 'case-documents/fir_2024_0456.pdf',
                        'size' => 1572864,
                        'type' => 'application/pdf'
                    ],
                    [
                        'name' => 'evidence_photos.zip',
                        'path' => 'case-documents/evidence_photos.zip',
                        'size' => 5242880,
                        'type' => 'application/zip'
                    ]
                ],
                'user_id' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'case_number' => 'CASE-2024-0003',
                'case_title' => 'Wilson Family Custody Case',
                'first_party_name' => 'Sarah Wilson',
                'first_party_contact' => '+3334445555',
                'first_party_cnic' => '11223-4455667-8',
                'first_party_address' => '321 Elm Street, Queens, NY',
                'second_party_name' => 'James Wilson',
                'second_party_contact' => '+6667778888',
                'second_party_cnic' => '99887-6655443-2',
                'second_party_address' => '654 Maple Drive, Queens, NY',
                'case_type' => 'family',
                'court_name' => 'District Court',
                'case_status' => 'hearing',
                'filing_date' => Carbon::now()->subDays(5),
                'hearing_date' => Carbon::now()->addDays(7),
                'case_notes' => 'Child custody dispute following divorce proceedings. Both parents seeking primary custody.',
                'case_details' => [
                    'relation_type' => 'Divorced Couple',
                    'marriage_certificate' => 'MC-2020-7890'
                ],
                'documents' => [
                    [
                        'name' => 'marriage_certificate.pdf',
                        'path' => 'case-documents/marriage_cert_mc7890.pdf',
                        'size' => 1048576,
                        'type' => 'application/pdf'
                    ],
                    [
                        'name' => 'custody_agreement.docx',
                        'path' => 'case-documents/custody_agreement.docx',
                        'size' => 524288,
                        'type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                    ]
                ],
                'user_id' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'case_number' => 'CASE-2024-0004',
                'case_title' => 'TechCorp vs. Innovate Solutions Contract Breach',
                'first_party_name' => 'TechCorp International',
                'first_party_contact' => '+7778889999',
                'first_party_cnic' => null,
                'first_party_address' => 'Tech Park, Silicon Valley, CA',
                'second_party_name' => 'Innovate Solutions LLC',
                'second_party_contact' => '+2223334444',
                'second_party_cnic' => null,
                'second_party_address' => 'Innovation Center, Boston, MA',
                'case_type' => 'commercial',
                'court_name' => 'High Court',
                'case_status' => 'closed',
                'filing_date' => Carbon::now()->subDays(60),
                'hearing_date' => Carbon::now()->subDays(15),
                'case_notes' => 'Commercial contract breach case settled out of court. Both parties reached mutual agreement.',
                'case_details' => [
                    'company_name' => 'TechCorp International',
                    'contract_ref' => 'TC-INN-2023-045'
                ],
                'documents' => [
                    [
                        'name' => 'contract_agreement.pdf',
                        'path' => 'case-documents/contract_tc_inn_045.pdf',
                        'size' => 3145728,
                        'type' => 'application/pdf'
                    ],
                    [
                        'name' => 'settlement_agreement.pdf',
                        'path' => 'case-documents/settlement_agreement.pdf',
                        'size' => 2097152,
                        'type' => 'application/pdf'
                    ]
                ],
                'user_id' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'case_number' => 'CASE-2024-0005',
                'case_title' => 'Constitutional Rights Petition - Freedom of Speech',
                'first_party_name' => 'Civil Liberties Union',
                'first_party_contact' => '+5556667777',
                'first_party_cnic' => null,
                'first_party_address' => 'Civil Rights Center, Washington DC',
                'second_party_name' => 'State Government',
                'second_party_contact' => '+8889990000',
                'second_party_cnic' => null,
                'second_party_address' => 'State Capitol Building, Washington DC',
                'case_type' => 'constitutional',
                'court_name' => 'Supreme Court',
                'case_status' => 'pending',
                'filing_date' => Carbon::now()->subDays(3),
                'hearing_date' => Carbon::now()->addDays(45),
                'case_notes' => 'Constitutional challenge regarding freedom of speech protections in digital media.',
                'case_details' => [
                    'article_reference' => 'First Amendment',
                    'petition_type' => 'Constitutional Challenge'
                ],
                'documents' => [
                    [
                        'name' => 'constitutional_petition.pdf',
                        'path' => 'case-documents/constitutional_petition_1st_amendment.pdf',
                        'size' => 4194304,
                        'type' => 'application/pdf'
                    ]
                ],
                'user_id' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'case_number' => 'CASE-2024-0006',
                'case_title' => 'Global Enterprises Merger Approval',
                'first_party_name' => 'Global Enterprises Inc.',
                'first_party_contact' => '+1112223333',
                'first_party_cnic' => null,
                'first_party_address' => 'Corporate Headquarters, Chicago, IL',
                'second_party_name' => 'MegaCorp Ltd.',
                'second_party_contact' => '+4445556666',
                'second_party_cnic' => null,
                'second_party_address' => 'Business Plaza, Chicago, IL',
                'case_type' => 'corporate',
                'court_name' => 'District Court',
                'case_status' => 'open',
                'filing_date' => Carbon::now()->subDays(2),
                'hearing_date' => Carbon::now()->addDays(21),
                'case_notes' => 'Corporate merger requiring court approval. Both companies have submitted required documentation.',
                'case_details' => [
                    'business_type' => 'Multinational Corporation',
                    'registration_number' => 'CORP-789456-2024'
                ],
                'documents' => [
                    [
                        'name' => 'merger_agreement.pdf',
                        'path' => 'case-documents/merger_agreement_global_megacorp.pdf',
                        'size' => 6291456,
                        'type' => 'application/pdf'
                    ],
                    [
                        'name' => 'financial_statements.pdf',
                        'path' => 'case-documents/financial_statements_q1_2024.pdf',
                        'size' => 3145728,
                        'type' => 'application/pdf'
                    ]
                ],
                'user_id' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'case_number' => 'CASE-2024-0007',
                'case_title' => 'Brown vs. City Council - Land Zoning Dispute',
                'first_party_name' => 'Emily Brown',
                'first_party_contact' => '+9998887777',
                'first_party_cnic' => '66778-8990011-2',
                'first_party_address' => '123 Garden Lane, Suburbia, TX',
                'second_party_name' => 'City Council',
                'second_party_contact' => '+3332221111',
                'second_party_cnic' => null,
                'second_party_address' => 'City Hall, Downtown, TX',
                'case_type' => 'civil',
                'court_name' => 'Session Court',
                'case_status' => 'hearing',
                'filing_date' => Carbon::now()->subDays(8),
                'hearing_date' => Carbon::now()->addDays(5),
                'case_notes' => 'Land zoning dispute between property owner and city council. Environmental impact assessment pending.',
                'case_details' => [
                    'court_type' => 'Administrative Court',
                    'dispute_details' => 'Zoning regulations and property rights'
                ],
                'documents' => [
                    [
                        'name' => 'zoning_appeal.pdf',
                        'path' => 'case-documents/zoning_appeal_brown_city.pdf',
                        'size' => 2621440,
                        'type' => 'application/pdf'
                    ]
                ],
                'user_id' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'case_number' => 'CASE-2024-0008',
                'case_title' => 'Johnson Estate Inheritance Case',
                'first_party_name' => 'Mary Johnson',
                'first_party_contact' => '+6665554444',
                'first_party_cnic' => '33445-5566778-9',
                'first_party_address' => '789 Heritage Road, Old Town, FL',
                'second_party_name' => 'Robert Johnson Jr.',
                'second_party_contact' => '+7776665555',
                'second_party_cnic' => '44556-6677889-0',
                'second_party_address' => '456 Legacy Avenue, New Town, FL',
                'case_type' => 'family',
                'court_name' => 'District Court',
                'case_status' => 'closed',
                'filing_date' => Carbon::now()->subDays(90),
                'hearing_date' => Carbon::now()->subDays(30),
                'case_notes' => 'Inheritance dispute between siblings. Case resolved with mediated settlement.',
                'case_details' => [
                    'relation_type' => 'Siblings',
                    'marriage_certificate' => null
                ],
                'documents' => [
                    [
                        'name' => 'will_document.pdf',
                        'path' => 'case-documents/will_johnson_estate.pdf',
                        'size' => 1572864,
                        'type' => 'application/pdf'
                    ],
                    [
                        'name' => 'settlement_agreement.pdf',
                        'path' => 'case-documents/inheritance_settlement.pdf',
                        'size' => 2097152,
                        'type' => 'application/pdf'
                    ]
                ],
                'user_id' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insert cases
        foreach ($cases as $caseData) {
            Case_Model::create($caseData);
        }

        $this->command->info('Successfully seeded ' . count($cases) . ' cases for user: ' . $user->email);
    }
}