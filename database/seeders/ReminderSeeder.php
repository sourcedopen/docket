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

        $reminders = [
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

            // Consumer complaint — hearing deadline
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

            // RTI — already sent reminder (past)
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
        ];

        foreach ($reminders as $reminder) {
            if ($reminder['ticket_id']) {
                Reminder::firstOrCreate(
                    [
                        'ticket_id' => $reminder['ticket_id'],
                        'title' => $reminder['title'],
                    ],
                    $reminder
                );
            }
        }
    }
}
