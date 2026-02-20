<?php

namespace Database\Seeders;

use App\Enums\ContactType;
use App\Models\Contact;
use Illuminate\Database\Seeder;
use SourcedOpen\Tags\Models\Tag;

class ContactSeeder extends Seeder
{
    public function run(): void
    {
        $contacts = [
            // Authority contacts — all fields populated
            [
                'name' => 'Central Public Information Officer',
                'designation' => 'CPIO',
                'organization' => 'Ministry of Home Affairs',
                'email' => 'cpio-mha@gov.in',
                'phone' => '011-23092011',
                'address' => 'North Block, New Delhi - 110001',
                'type' => ContactType::Authority->value,
                'notes' => 'Handles RTI applications for MHA.',
                'tags' => ['government', 'rti'],
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
                'tags' => ['telecom', 'dnd'],
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
                'tags' => ['banking', 'finance'],
            ],

            // Company contacts — some optional fields null
            [
                'name' => 'Reliance Jio Customer Care',
                'designation' => 'Nodal Officer',
                'organization' => 'Reliance Jio Infocomm Ltd.',
                'email' => 'nodal.officer@jio.com',
                'phone' => '198',
                'address' => null,
                'type' => ContactType::Company->value,
                'notes' => null,
                'tags' => ['telecom', 'isp'],
            ],
            [
                'name' => 'TechCorp India Support',
                'designation' => 'Customer Support Manager',
                'organization' => 'TechCorp India Pvt Ltd',
                'email' => 'support@techcorp.in',
                'phone' => '1800-123-4567',
                'address' => 'Tech Park, Whitefield, Bangalore - 560066',
                'type' => ContactType::Company->value,
                'notes' => 'Laptop manufacturer. Warranty issues escalated here.',
                'tags' => ['electronics', 'warranty'],
            ],

            // Department contacts
            [
                'name' => 'District Consumer Forum',
                'designation' => 'Registrar',
                'organization' => 'District Consumer Disputes Redressal Forum',
                'email' => null,
                'phone' => null,
                'address' => 'Collectorate Complex, Pune - 411001',
                'type' => ContactType::Department->value,
                'notes' => 'Filing counter open Mon-Fri 10am-5pm.',
                'tags' => ['consumer', 'legal'],
            ],
            [
                'name' => 'Municipal Corporation Ward Office',
                'designation' => 'Ward Officer',
                'organization' => 'Pune Municipal Corporation',
                'email' => 'ward15@pmc.gov.in',
                'phone' => '020-25501000',
                'address' => 'Ward Office No. 15, Kothrud, Pune',
                'type' => ContactType::Department->value,
                'notes' => 'Handles civic complaints for Kothrud ward.',
                'tags' => ['civic', 'municipal'],
            ],

            // Individual contacts
            [
                'name' => 'Rajesh Sharma',
                'designation' => 'Advocate',
                'organization' => null,
                'email' => 'rajesh.sharma@lawfirm.in',
                'phone' => '9876543210',
                'address' => 'B-12, Court Complex, Pune',
                'type' => ContactType::Individual->value,
                'notes' => 'Consumer law specialist. Consulted for escalation cases.',
                'tags' => ['legal', 'advocate'],
            ],
            // Minimal data contact — only required fields
            [
                'name' => 'Anonymous Tipline',
                'designation' => null,
                'organization' => null,
                'email' => null,
                'phone' => '112',
                'address' => null,
                'type' => ContactType::Individual->value,
                'notes' => null,
                'tags' => [],
            ],

            // Soft-deleted contact — organization no longer relevant
            [
                'name' => 'Old Broadband Provider',
                'designation' => 'Support Desk',
                'organization' => 'ConnectNet Broadband',
                'email' => 'support@connectnet.in',
                'phone' => '1800-999-0000',
                'address' => null,
                'type' => ContactType::Company->value,
                'notes' => 'Discontinued service. No longer needed.',
                'tags' => ['telecom'],
                'deleted' => true,
            ],
        ];

        foreach ($contacts as $data) {
            $tagNames = $data['tags'] ?? [];
            $isDeleted = $data['deleted'] ?? false;
            unset($data['tags'], $data['deleted']);

            $contact = Contact::withTrashed()->firstOrCreate(['name' => $data['name']], $data);

            if ($tagNames) {
                $tagIds = collect($tagNames)
                    ->map(fn ($name) => Tag::firstOrCreate(['name' => $name], ['color' => sprintf('#%06X', mt_rand(0, 0xFFFFFF))])->id)
                    ->toArray();
                $contact->syncTags($tagIds);
            }

            if ($isDeleted && ! $contact->trashed()) {
                $contact->delete();
            }
        }
    }
}
