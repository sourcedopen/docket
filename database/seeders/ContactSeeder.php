<?php

namespace Database\Seeders;

use App\Enums\ContactType;
use App\Models\Contact;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    public function run(): void
    {
        $contacts = [
            [
                'name' => 'Central Public Information Officer',
                'designation' => 'CPIO',
                'organization' => 'Ministry of Home Affairs',
                'email' => 'cpio-mha@gov.in',
                'phone' => '011-23092011',
                'address' => 'North Block, New Delhi - 110001',
                'type' => ContactType::Authority->value,
                'notes' => 'Handles RTI applications for MHA.',
            ],
            [
                'name' => 'TRAI Consumer Cell',
                'designation' => null,
                'organization' => 'Telecom Regulatory Authority of India',
                'email' => 'dnd@trai.gov.in',
                'phone' => '1909',
                'address' => 'Mahanagar Doorsanchar Bhawan, New Delhi',
                'type' => ContactType::Authority->value,
                'notes' => 'DND complaint registration and tracking.',
            ],
            [
                'name' => 'Reliance Jio Customer Care',
                'designation' => 'Nodal Officer',
                'organization' => 'Reliance Jio Infocomm Ltd.',
                'email' => 'nodal.officer@jio.com',
                'phone' => '198',
                'address' => null,
                'type' => ContactType::Company->value,
                'notes' => null,
            ],
            [
                'name' => 'District Consumer Forum',
                'designation' => 'Registrar',
                'organization' => 'District Consumer Disputes Redressal Forum',
                'email' => null,
                'phone' => null,
                'address' => 'Collectorate Complex, Pune - 411001',
                'type' => ContactType::Department->value,
                'notes' => 'Filing counter open Mon-Fri 10am-5pm.',
            ],
            [
                'name' => 'RBI Ombudsman Office',
                'designation' => 'Banking Ombudsman',
                'organization' => 'Reserve Bank of India',
                'email' => 'crpc@rbi.org.in',
                'phone' => '14448',
                'address' => 'RBI Main Building, Mumbai - 400001',
                'type' => ContactType::Authority->value,
                'notes' => 'CMS portal: https://cms.rbi.org.in',
            ],
            [
                'name' => 'Rajesh Sharma',
                'designation' => 'Advocate',
                'organization' => null,
                'email' => 'rajesh.sharma@lawfirm.in',
                'phone' => '9876543210',
                'address' => 'B-12, Court Complex, Pune',
                'type' => ContactType::Individual->value,
                'notes' => 'Consumer law specialist. Consulted for escalation cases.',
            ],
        ];

        foreach ($contacts as $contact) {
            Contact::firstOrCreate(['name' => $contact['name']], $contact);
        }
    }
}
