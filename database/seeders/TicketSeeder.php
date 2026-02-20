<?php

namespace Database\Seeders;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Contact;
use App\Models\Ticket;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Database\Seeder;
use SourcedOpen\Tags\Models\Tag;

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
        $techCorp = Contact::where('name', 'TechCorp India Support')->first();
        $municipal = Contact::where('name', 'Municipal Corporation Ward Office')->first();

        $tickets = [];

        // 1. Draft RTI — just started, not yet submitted, no filed date, no due date
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
                'external_reference' => null,
                'custom_fields' => [
                    'pio_name' => 'Shri R.K. Verma',
                    'department' => 'Ministry of Home Affairs',
                    'fee_paid' => 10,
                    'mode_of_filing' => 'online_portal',
                ],
            ]
        );
        $this->syncTags($tickets['rti_draft'], ['rti', 'procurement']);

        // 2. Submitted DND complaint — with external reference, awaiting acknowledgement
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
                'external_reference' => 'DND-TRAI-2026-78432',
                'custom_fields' => [
                    'operator_name' => 'Reliance Jio',
                    'complaint_number' => 'DND-2026-78432',
                    'phone_number' => '9876543210',
                    'type_of_violation' => 'promotional_call',
                ],
            ]
        );
        $this->syncTags($tickets['dnd_submitted'], ['telecom', 'spam', 'urgent']);

        // 3. Acknowledged DND complaint — second complaint, SMS spam
        $tickets['dnd_acknowledged'] = Ticket::firstOrCreate(
            ['reference_number' => 'TKT-2026-0009'],
            [
                'user_id' => $user->id,
                'ticket_type_id' => $dndType?->id,
                'title' => 'Promotional SMS despite DND activation',
                'description' => 'Receiving bulk promotional SMS from unknown senders daily. Reported multiple headers.',
                'status' => TicketStatus::Acknowledged->value,
                'priority' => TicketPriority::Low->value,
                'filed_with_contact_id' => $jio?->id,
                'filed_date' => now()->subDays(7)->toDateString(),
                'due_date' => null,
                'external_reference' => 'JIO-DND-2026-55123',
                'custom_fields' => [
                    'operator_name' => 'Reliance Jio',
                    'complaint_number' => 'DND-2026-55123',
                    'phone_number' => '9876543210',
                    'type_of_violation' => 'promotional_sms',
                ],
            ]
        );
        $this->syncTags($tickets['dnd_acknowledged'], ['telecom', 'sms']);

        // 4. In-progress consumer complaint — actively being worked on, with external_reference
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
                'external_reference' => 'CC/2026/0342',
                'custom_fields' => [
                    'company_name' => 'TechCorp India Pvt Ltd',
                    'product_or_service' => 'Laptop Model X15 Pro',
                    'amount_involved' => 85000,
                    'complaint_forum' => 'district',
                ],
            ]
        );
        $this->syncTags($tickets['consumer_in_progress'], ['consumer', 'electronics', 'refund']);

        // 5. Escalated banking complaint — critical priority, needs urgent attention
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
                'external_reference' => 'CMS-2026-MH-00987',
                'custom_fields' => [
                    'bank_name' => 'State Bank of India',
                    'account_type' => 'savings',
                    'complaint_category' => 'Unauthorized Transaction',
                    'amount_disputed' => 25000,
                ],
            ]
        );
        $this->syncTags($tickets['banking_escalated'], ['banking', 'fraud', 'critical']);

        // 6. Resolved insurance complaint — successfully closed, with all date fields
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
                'external_reference' => 'IGMS-2026-LF-04521',
                'custom_fields' => [
                    'insurer_name' => 'National Insurance Co.',
                    'policy_number' => 'HLT-2024-98765',
                    'claim_number' => 'CLM-2026-54321',
                    'claim_amount' => 150000,
                ],
            ]
        );
        $this->syncTags($tickets['insurance_resolved'], ['insurance', 'health', 'resolved']);

        // 7. Closed general complaint — no custom fields, no external reference, no contact
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
                'external_reference' => null,
                'custom_fields' => null,
            ]
        );
        $this->syncTags($tickets['general_closed'], ['utility', 'billing']);

        // 8. Reopened RTI — first appeal filed, has original filed date far back
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
                'external_reference' => 'RTIMH/2026/00145',
                'custom_fields' => [
                    'pio_name' => 'Shri A.K. Mishra',
                    'department' => 'Ministry of Home Affairs',
                    'fee_paid' => 10,
                    'mode_of_filing' => 'speed_post',
                    'first_appeal_deadline' => now()->addDays(10)->toDateString(),
                ],
            ]
        );
        $this->syncTags($tickets['rti_reopened'], ['rti', 'appeal', 'police']);

        // 9. Child ticket of consumer complaint (#4) — tracks a sub-task
        $tickets['consumer_child'] = Ticket::firstOrCreate(
            ['reference_number' => 'TKT-2026-0008'],
            [
                'user_id' => $user->id,
                'ticket_type_id' => $consumerType?->id,
                'title' => 'Follow-up: Laptop service center inspection report',
                'description' => 'Sub-ticket to track the service center inspection report requested by the consumer forum.',
                'status' => TicketStatus::Acknowledged->value,
                'priority' => TicketPriority::Medium->value,
                'filed_with_contact_id' => $techCorp?->id,
                'filed_date' => now()->subDays(10)->toDateString(),
                'due_date' => now()->addDays(5)->toDateString(),
                'external_reference' => null,
                'parent_ticket_id' => $tickets['consumer_in_progress']->id,
            ]
        );
        $this->syncTags($tickets['consumer_child'], ['consumer', 'follow-up']);

        // 10. General complaint with no description, no contact, no custom fields — truly minimal
        $tickets['general_minimal'] = Ticket::firstOrCreate(
            ['reference_number' => 'TKT-2026-0010'],
            [
                'user_id' => $user->id,
                'ticket_type_id' => $generalType?->id,
                'title' => 'Parking fine dispute',
                'description' => null,
                'status' => TicketStatus::Draft->value,
                'priority' => TicketPriority::Low->value,
                'filed_with_contact_id' => null,
                'filed_date' => null,
                'due_date' => null,
                'external_reference' => null,
                'custom_fields' => null,
            ]
        );

        // 11. Civic complaint filed with municipal body — in progress
        $tickets['civic_in_progress'] = Ticket::firstOrCreate(
            ['reference_number' => 'TKT-2026-0011'],
            [
                'user_id' => $user->id,
                'ticket_type_id' => $generalType?->id,
                'title' => 'Pothole on main road causing accidents',
                'description' => 'Large pothole near Kothrud bus stop has caused at least 2 bike accidents this week. Reported on PMC portal but no action taken.',
                'status' => TicketStatus::InProgress->value,
                'priority' => TicketPriority::High->value,
                'filed_with_contact_id' => $municipal?->id,
                'filed_date' => now()->subDays(5)->toDateString(),
                'due_date' => now()->addDays(10)->toDateString(),
                'external_reference' => 'PMC-GRV-2026-12345',
                'custom_fields' => null,
            ]
        );
        $this->syncTags($tickets['civic_in_progress'], ['civic', 'roads', 'safety']);

        // 12. Overdue ticket — past due date, still in progress
        $tickets['consumer_overdue'] = Ticket::firstOrCreate(
            ['reference_number' => 'TKT-2025-0012'],
            [
                'user_id' => $user->id,
                'ticket_type_id' => $consumerType?->id,
                'title' => 'Refund not received for cancelled order',
                'description' => 'Cancelled an online order 60 days ago. Refund of ₹3,500 still pending despite multiple follow-ups with the seller.',
                'status' => TicketStatus::InProgress->value,
                'priority' => TicketPriority::Medium->value,
                'filed_with_contact_id' => null,
                'filed_date' => now()->subDays(60)->toDateString(),
                'due_date' => now()->subDays(15)->toDateString(),
                'external_reference' => null,
                'custom_fields' => [
                    'company_name' => 'QuickShop Online',
                    'product_or_service' => 'Electronics Accessories',
                    'amount_involved' => 3500,
                    'complaint_forum' => 'district',
                ],
            ]
        );
        $this->syncTags($tickets['consumer_overdue'], ['consumer', 'refund', 'overdue']);

        // 13. Soft-deleted ticket — user decided to withdraw
        $deletedTicket = Ticket::withTrashed()->firstOrCreate(
            ['reference_number' => 'TKT-2025-0013'],
            [
                'user_id' => $user->id,
                'ticket_type_id' => $generalType?->id,
                'title' => 'Noise complaint against neighbour — withdrawn',
                'description' => 'Filed noise complaint but resolved amicably with neighbour. Withdrawing this ticket.',
                'status' => TicketStatus::Draft->value,
                'priority' => TicketPriority::Low->value,
                'filed_with_contact_id' => null,
                'filed_date' => now()->subDays(30)->toDateString(),
                'due_date' => null,
                'external_reference' => null,
                'custom_fields' => null,
            ]
        );
        if (! $deletedTicket->trashed()) {
            $deletedTicket->delete();
        }

        // 14. Banking complaint with loan account type — different custom field values
        $tickets['banking_loan'] = Ticket::firstOrCreate(
            ['reference_number' => 'TKT-2026-0014'],
            [
                'user_id' => $user->id,
                'ticket_type_id' => $bankingType?->id,
                'title' => 'Excessive processing fee charged on home loan',
                'description' => 'Bank charged ₹15,000 processing fee instead of the agreed ₹5,000 mentioned in the sanction letter.',
                'status' => TicketStatus::Submitted->value,
                'priority' => TicketPriority::Medium->value,
                'filed_with_contact_id' => $rbiOmbudsman?->id,
                'filed_date' => now()->subDays(2)->toDateString(),
                'due_date' => now()->addDays(28)->toDateString(),
                'external_reference' => null,
                'custom_fields' => [
                    'bank_name' => 'HDFC Bank',
                    'account_type' => 'loan',
                    'complaint_category' => 'Excessive Charges',
                    'amount_disputed' => 10000,
                ],
            ]
        );
        $this->syncTags($tickets['banking_loan'], ['banking', 'loan', 'charges']);
    }

    /** @param array<string> $names */
    private function syncTags(Ticket $ticket, array $names): void
    {
        $tagIds = collect($names)
            ->map(fn ($name) => Tag::firstOrCreate(['name' => $name], ['color' => sprintf('#%06X', mt_rand(0, 0xFFFFFF))])->id)
            ->toArray();
        $ticket->syncTags($tagIds);
    }
}
