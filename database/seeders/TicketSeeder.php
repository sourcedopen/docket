<?php

namespace Database\Seeders;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Contact;
use App\Models\Ticket;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'test@example.com')->firstOrFail();

        $rtiType = TicketType::where('slug', 'rti-application')->first();
        $dndType = TicketType::where('slug', 'dnd-complaint')->first();
        $consumerType = TicketType::where('slug', 'consumer-complaint')->first();
        $bankingType = TicketType::where('slug', 'banking-ombudsman')->first();
        $insuranceType = TicketType::where('slug', 'insurance-complaint')->first();
        $generalType = TicketType::where('slug', 'general-complaint')->first();

        $cpio = Contact::where('name', 'Central Public Information Officer')->first();
        $trai = Contact::where('name', 'TRAI Consumer Cell')->first();
        $jio = Contact::where('name', 'Reliance Jio Customer Care')->first();
        $consumerForum = Contact::where('name', 'District Consumer Forum')->first();
        $rbiOmbudsman = Contact::where('name', 'RBI Ombudsman Office')->first();

        // 1. Draft RTI — just started, not yet submitted
        $tickets['rti_draft'] = Ticket::firstOrCreate(
            ['reference_number' => 'TKT-2026-0001'],
            [
                'user_id' => $user->id,
                'ticket_type_id' => $rtiType?->id,
                'title' => 'RTI on public procurement data for FY 2024-25',
                'description' => 'Requesting details of all tenders awarded above ₹1 crore by MHA in FY 2024-25.',
                'status' => TicketStatus::Draft->value,
                'priority' => TicketPriority::Medium->value,
                'filed_with_contact_id' => $cpio?->id,
                'filed_date' => null,
                'due_date' => null,
                'custom_fields' => [
                    'pio_name' => 'Shri R.K. Verma',
                    'department' => 'Ministry of Home Affairs',
                    'fee_paid' => 10,
                    'mode_of_filing' => 'online_portal',
                ],
            ]
        );

        // 2. Submitted DND complaint — awaiting acknowledgement
        $tickets['dnd_submitted'] = Ticket::firstOrCreate(
            ['reference_number' => 'TKT-2026-0002'],
            [
                'user_id' => $user->id,
                'ticket_type_id' => $dndType?->id,
                'title' => 'Repeated spam calls from insurance agents',
                'description' => 'Receiving 5-6 promotional calls daily despite active DND. Complaint registered with operator but no resolution.',
                'status' => TicketStatus::Submitted->value,
                'priority' => TicketPriority::High->value,
                'filed_with_contact_id' => $trai?->id,
                'filed_date' => now()->subDays(3)->toDateString(),
                'due_date' => now()->addDays(4)->toDateString(),
                'custom_fields' => [
                    'operator_name' => 'Reliance Jio',
                    'complaint_number' => 'DND-2026-78432',
                    'phone_number' => '9876543210',
                    'type_of_violation' => 'promotional_call',
                ],
            ]
        );

        // 3. In-progress consumer complaint — actively being worked on
        $tickets['consumer_in_progress'] = Ticket::firstOrCreate(
            ['reference_number' => 'TKT-2026-0003'],
            [
                'user_id' => $user->id,
                'ticket_type_id' => $consumerType?->id,
                'title' => 'Defective laptop — refund denied by manufacturer',
                'description' => 'Purchased laptop developed motherboard issues within 3 months. Company refused refund citing physical damage which did not occur.',
                'status' => TicketStatus::InProgress->value,
                'priority' => TicketPriority::High->value,
                'filed_with_contact_id' => $consumerForum?->id,
                'filed_date' => now()->subDays(20)->toDateString(),
                'due_date' => now()->addDays(25)->toDateString(),
                'custom_fields' => [
                    'company_name' => 'TechCorp India Pvt Ltd',
                    'product_or_service' => 'Laptop Model X15 Pro',
                    'amount_involved' => 85000,
                    'complaint_forum' => 'district',
                ],
            ]
        );

        // 4. Escalated banking complaint — needs urgent attention
        $tickets['banking_escalated'] = Ticket::firstOrCreate(
            ['reference_number' => 'TKT-2026-0004'],
            [
                'user_id' => $user->id,
                'ticket_type_id' => $bankingType?->id,
                'title' => 'Unauthorized debit of ₹25,000 from savings account',
                'description' => 'Two unauthorized transactions appeared on my statement. Bank has not reversed the amount despite filing complaint 15 days ago.',
                'status' => TicketStatus::Escalated->value,
                'priority' => TicketPriority::Critical->value,
                'filed_with_contact_id' => $rbiOmbudsman?->id,
                'filed_date' => now()->subDays(15)->toDateString(),
                'due_date' => now()->addDays(15)->toDateString(),
                'custom_fields' => [
                    'bank_name' => 'State Bank of India',
                    'account_type' => 'savings',
                    'complaint_category' => 'Unauthorized Transaction',
                    'amount_disputed' => 25000,
                ],
            ]
        );

        // 5. Resolved insurance complaint — successfully closed
        $tickets['insurance_resolved'] = Ticket::firstOrCreate(
            ['reference_number' => 'TKT-2026-0005'],
            [
                'user_id' => $user->id,
                'ticket_type_id' => $insuranceType?->id,
                'title' => 'Health insurance claim rejected without valid reason',
                'description' => 'Claim for hospitalization rejected citing pre-existing condition. Policy clearly covers this after 2-year waiting period which has passed.',
                'status' => TicketStatus::Resolved->value,
                'priority' => TicketPriority::Medium->value,
                'filed_with_contact_id' => null,
                'filed_date' => now()->subDays(45)->toDateString(),
                'due_date' => now()->subDays(30)->toDateString(),
                'closed_date' => now()->subDays(5)->toDateString(),
                'custom_fields' => [
                    'insurer_name' => 'National Insurance Co.',
                    'policy_number' => 'HLT-2024-98765',
                    'claim_number' => 'CLM-2026-54321',
                    'claim_amount' => 150000,
                ],
            ]
        );

        // 6. Closed general complaint
        $tickets['general_closed'] = Ticket::firstOrCreate(
            ['reference_number' => 'TKT-2025-0006'],
            [
                'user_id' => $user->id,
                'ticket_type_id' => $generalType?->id,
                'title' => 'Incorrect electricity bill for Jan 2025',
                'description' => 'Bill amount was 3x the usual consumption. Meter reading was verified and bill corrected after complaint.',
                'status' => TicketStatus::Closed->value,
                'priority' => TicketPriority::Low->value,
                'filed_with_contact_id' => null,
                'filed_date' => now()->subMonths(2)->toDateString(),
                'due_date' => now()->subMonths(1)->toDateString(),
                'closed_date' => now()->subDays(40)->toDateString(),
            ]
        );

        // 7. Reopened RTI — first appeal filed
        $tickets['rti_reopened'] = Ticket::firstOrCreate(
            ['reference_number' => 'TKT-2026-0007'],
            [
                'user_id' => $user->id,
                'ticket_type_id' => $rtiType?->id,
                'title' => 'RTI on police recruitment exam results — first appeal',
                'description' => 'Original RTI response was inadequate. Filing first appeal as many details were denied without valid Section 8 exemption.',
                'status' => TicketStatus::Reopened->value,
                'priority' => TicketPriority::High->value,
                'filed_with_contact_id' => $cpio?->id,
                'filed_date' => now()->subDays(50)->toDateString(),
                'due_date' => now()->addDays(10)->toDateString(),
                'custom_fields' => [
                    'pio_name' => 'Shri A.K. Mishra',
                    'department' => 'Ministry of Home Affairs',
                    'fee_paid' => 10,
                    'mode_of_filing' => 'speed_post',
                    'first_appeal_deadline' => now()->addDays(10)->toDateString(),
                ],
            ]
        );

        // 8. Consumer complaint with parent-child relationship (sub-ticket of #3)
        Ticket::firstOrCreate(
            ['reference_number' => 'TKT-2026-0008'],
            [
                'user_id' => $user->id,
                'ticket_type_id' => $consumerType?->id,
                'title' => 'Follow-up: Laptop service center inspection report',
                'description' => 'Sub-ticket to track the service center inspection report requested by the consumer forum.',
                'status' => TicketStatus::Acknowledged->value,
                'priority' => TicketPriority::Medium->value,
                'filed_with_contact_id' => $jio?->id,
                'filed_date' => now()->subDays(10)->toDateString(),
                'due_date' => now()->addDays(5)->toDateString(),
                'parent_ticket_id' => $tickets['consumer_in_progress']->id,
            ]
        );
    }
}
