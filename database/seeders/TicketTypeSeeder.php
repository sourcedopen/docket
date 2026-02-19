<?php

namespace Database\Seeders;

use App\Models\TicketType;
use Illuminate\Database\Seeder;

class TicketTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'name' => 'RTI Application',
                'slug' => 'rti-application',
                'description' => 'Right to Information applications filed with government departments.',
                'default_sla_days' => 30,
                'sort_order' => 1,
                'schema_definition' => [
                    'fields' => [
                        ['key' => 'pio_name', 'label' => 'PIO Name', 'type' => 'string', 'required' => true],
                        ['key' => 'department', 'label' => 'Department', 'type' => 'string', 'required' => false],
                        ['key' => 'fee_paid', 'label' => 'Fee Paid (₹)', 'type' => 'number', 'default' => 10],
                        ['key' => 'mode_of_filing', 'label' => 'Mode of Filing', 'type' => 'select', 'options' => ['online_portal', 'speed_post', 'in_person'], 'required' => true],
                        ['key' => 'first_appeal_deadline', 'label' => 'First Appeal Deadline', 'type' => 'date'],
                    ],
                ],
            ],
            [
                'name' => 'DND Complaint (TRAI)',
                'slug' => 'dnd-complaint',
                'description' => 'Do Not Disturb complaints filed with TRAI.',
                'default_sla_days' => 7,
                'sort_order' => 2,
                'schema_definition' => [
                    'fields' => [
                        ['key' => 'operator_name', 'label' => 'Operator Name', 'type' => 'string', 'required' => true],
                        ['key' => 'complaint_number', 'label' => 'Complaint Number', 'type' => 'string'],
                        ['key' => 'phone_number', 'label' => 'Phone Number', 'type' => 'string', 'required' => true],
                        ['key' => 'type_of_violation', 'label' => 'Type of Violation', 'type' => 'select', 'options' => ['promotional_call', 'promotional_sms', 'spam'], 'required' => false],
                    ],
                ],
            ],
            [
                'name' => 'Consumer Complaint',
                'slug' => 'consumer-complaint',
                'description' => 'Consumer grievances filed with consumer forums.',
                'default_sla_days' => 45,
                'sort_order' => 3,
                'schema_definition' => [
                    'fields' => [
                        ['key' => 'company_name', 'label' => 'Company Name', 'type' => 'string', 'required' => true],
                        ['key' => 'product_or_service', 'label' => 'Product / Service', 'type' => 'string'],
                        ['key' => 'amount_involved', 'label' => 'Amount Involved (₹)', 'type' => 'number'],
                        ['key' => 'complaint_forum', 'label' => 'Complaint Forum', 'type' => 'select', 'options' => ['district', 'state', 'national']],
                    ],
                ],
            ],
            [
                'name' => 'Banking Ombudsman',
                'slug' => 'banking-ombudsman',
                'description' => 'Banking complaints filed with RBI Ombudsman.',
                'default_sla_days' => 30,
                'sort_order' => 4,
                'schema_definition' => [
                    'fields' => [
                        ['key' => 'bank_name', 'label' => 'Bank Name', 'type' => 'string', 'required' => true],
                        ['key' => 'account_type', 'label' => 'Account Type', 'type' => 'select', 'options' => ['savings', 'current', 'loan', 'credit_card']],
                        ['key' => 'complaint_category', 'label' => 'Complaint Category', 'type' => 'string'],
                        ['key' => 'amount_disputed', 'label' => 'Amount Disputed (₹)', 'type' => 'number'],
                    ],
                ],
            ],
            [
                'name' => 'Insurance Complaint',
                'slug' => 'insurance-complaint',
                'description' => 'Insurance grievances filed with IRDAI.',
                'default_sla_days' => 15,
                'sort_order' => 5,
                'schema_definition' => [
                    'fields' => [
                        ['key' => 'insurer_name', 'label' => 'Insurer Name', 'type' => 'string', 'required' => true],
                        ['key' => 'policy_number', 'label' => 'Policy Number', 'type' => 'string'],
                        ['key' => 'claim_number', 'label' => 'Claim Number', 'type' => 'string'],
                        ['key' => 'claim_amount', 'label' => 'Claim Amount (₹)', 'type' => 'number'],
                    ],
                ],
            ],
            [
                'name' => 'General Complaint',
                'slug' => 'general-complaint',
                'description' => 'General complaints not covered by other types.',
                'default_sla_days' => null,
                'sort_order' => 6,
                'schema_definition' => ['fields' => []],
            ],
        ];

        foreach ($types as $type) {
            TicketType::firstOrCreate(['slug' => $type['slug']], $type);
        }
    }
}
