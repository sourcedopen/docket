<?php

namespace Database\Seeders;

use App\Enums\ReminderType;
use App\Models\Reminder;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReminderSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'test@example.com')->firstOrFail();

        $dndTicket = Ticket::where('reference_number', 'TKT-2026-0002')->first();
        $consumerTicket = Ticket::where('reference_number', 'TKT-2026-0003')->first();
        $bankingTicket = Ticket::where('reference_number', 'TKT-2026-0004')->first();
        $rtiReopenedTicket = Ticket::where('reference_number', 'TKT-2026-0007')->first();
        $civicTicket = Ticket::where('reference_number', 'TKT-2026-0011')->first();
        $overdueTicket = Ticket::where('reference_number', 'TKT-2025-0012')->first();
        $bankingLoanTicket = Ticket::where('reference_number', 'TKT-2026-0014')->first();

        $reminders = [
            // --- Future, not sent ---

            // DND — follow up if no response
            [
                'ticket_id' => $dndTicket?->id,
                'user_id' => $user->id,
                'title' => 'Follow up on TRAI DND complaint status',
                'remind_at' => now()->addDays(2),
                'type' => ReminderType::FollowUp->value,
                'notes' => 'Check TRAI portal for complaint status update.',
                'is_sent' => false,
                'is_recurring' => false,
            ],

            // Consumer complaint — hearing deadline approaching
            [
                'ticket_id' => $consumerTicket?->id,
                'user_id' => $user->id,
                'title' => 'Consumer forum hearing approaching',
                'remind_at' => now()->addDays(18),
                'type' => ReminderType::DeadlineApproaching->value,
                'notes' => 'Prepare all documents: purchase invoice, warranty card, service report, legal notice copy.',
                'is_sent' => false,
                'is_recurring' => false,
            ],

            // Banking — urgent deadline
            [
                'ticket_id' => $bankingTicket?->id,
                'user_id' => $user->id,
                'title' => 'Banking ombudsman response deadline',
                'remind_at' => now()->addDays(5),
                'type' => ReminderType::DeadlineApproaching->value,
                'notes' => 'If no resolution by this date, file formal complaint with Banking Ombudsman for adjudication.',
                'is_sent' => false,
                'is_recurring' => false,
            ],

            // RTI — first appeal deadline
            [
                'ticket_id' => $rtiReopenedTicket?->id,
                'user_id' => $user->id,
                'title' => 'RTI first appeal submission deadline',
                'remind_at' => now()->addDays(8),
                'type' => ReminderType::DeadlineApproaching->value,
                'notes' => 'First appeal must be filed within 30 days of receiving original response. Deadline approaching.',
                'is_sent' => false,
                'is_recurring' => false,
            ],

            // Civic — check repair status
            [
                'ticket_id' => $civicTicket?->id,
                'user_id' => $user->id,
                'title' => 'Check pothole repair status on site',
                'remind_at' => now()->addDays(6),
                'type' => ReminderType::FollowUp->value,
                'notes' => 'Visit the location and take photos to confirm if repair work was done.',
                'is_sent' => false,
                'is_recurring' => false,
            ],

            // Banking loan — custom reminder with no notes
            [
                'ticket_id' => $bankingLoanTicket?->id,
                'user_id' => $user->id,
                'title' => 'Call HDFC branch regarding processing fee reversal',
                'remind_at' => now()->addDays(3),
                'type' => ReminderType::Custom->value,
                'notes' => null,
                'is_sent' => false,
                'is_recurring' => false,
            ],

            // --- Recurring reminders ---

            // Consumer complaint — recurring weekly follow-up
            [
                'ticket_id' => $consumerTicket?->id,
                'user_id' => $user->id,
                'title' => 'Weekly check on consumer case progress',
                'remind_at' => now()->addDays(7),
                'type' => ReminderType::FollowUp->value,
                'notes' => 'Review case status and check for any communication from TechCorp.',
                'is_sent' => false,
                'is_recurring' => true,
                'recurrence_rule' => 'FREQ=WEEKLY;BYDAY=MO',
                'recurrence_ends_at' => now()->addMonths(2),
            ],

            // Overdue ticket — recurring bi-weekly escalation follow-up
            [
                'ticket_id' => $overdueTicket?->id,
                'user_id' => $user->id,
                'title' => 'Follow up on QuickShop refund',
                'remind_at' => now()->addDays(3),
                'type' => ReminderType::FollowUp->value,
                'notes' => 'Call QuickShop and document each conversation with date and reference number.',
                'is_sent' => false,
                'is_recurring' => true,
                'recurrence_rule' => 'FREQ=WEEKLY;INTERVAL=2;BYDAY=WE',
                'recurrence_ends_at' => now()->addMonths(1),
            ],

            // --- Already sent reminders (past) ---

            // RTI — past reminder, already sent
            [
                'ticket_id' => $rtiReopenedTicket?->id,
                'user_id' => $user->id,
                'title' => 'Collect original RTI response from post office',
                'remind_at' => now()->subDays(18),
                'type' => ReminderType::Custom->value,
                'notes' => 'Speed post article arrived. Need to collect from post office before it is returned.',
                'is_sent' => true,
                'sent_at' => now()->subDays(18),
                'is_recurring' => false,
            ],

            // Consumer — past deadline reminder, already sent
            [
                'ticket_id' => $consumerTicket?->id,
                'user_id' => $user->id,
                'title' => 'Legal notice response period ended',
                'remind_at' => now()->subDays(3),
                'type' => ReminderType::DeadlineApproaching->value,
                'notes' => '15-day legal notice response period has lapsed. Proceed with consumer forum filing.',
                'is_sent' => true,
                'sent_at' => now()->subDays(3),
                'is_recurring' => false,
            ],

            // Banking — past follow-up, already sent
            [
                'ticket_id' => $bankingTicket?->id,
                'user_id' => $user->id,
                'title' => 'Check SBI investigation update',
                'remind_at' => now()->subDays(5),
                'type' => ReminderType::FollowUp->value,
                'notes' => 'Call SBI branch and ask for investigation progress report.',
                'is_sent' => true,
                'sent_at' => now()->subDays(5),
                'is_recurring' => false,
            ],

            // --- Soft-deleted reminder ---
            [
                'ticket_id' => $dndTicket?->id,
                'user_id' => $user->id,
                'title' => 'Duplicate reminder — removed',
                'remind_at' => now()->addDays(2),
                'type' => ReminderType::Custom->value,
                'notes' => 'Created by mistake, removing.',
                'is_sent' => false,
                'is_recurring' => false,
                'deleted' => true,
            ],
        ];

        foreach ($reminders as $data) {
            if (! $data['ticket_id']) {
                continue;
            }

            $isDeleted = $data['deleted'] ?? false;
            unset($data['deleted']);

            $reminder = Reminder::withTrashed()->firstOrCreate(
                [
                    'ticket_id' => $data['ticket_id'],
                    'title' => $data['title'],
                ],
                $data
            );

            if ($isDeleted && ! $reminder->trashed()) {
                $reminder->delete();
            }
        }
    }
}
