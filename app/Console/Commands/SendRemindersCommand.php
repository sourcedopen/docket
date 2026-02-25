<?php

namespace App\Console\Commands;

use App\Enums\TicketStatus;
use App\Models\Reminder;
use App\Notifications\ReminderDueNotification;
use Illuminate\Console\Command;

class SendRemindersCommand extends Command
{
    protected $signature = 'reminders:send';

    protected $description = 'Send notifications for due reminders';

    public function handle(): int
    {
        $reminders = Reminder::query()
            ->with(['ticket.childTickets', 'user'])
            ->where('is_sent', false)
            ->where('remind_at', '<=', now())
            ->whereNull('deleted_at')
            ->get();

        $count = 0;

        foreach ($reminders as $reminder) {
            if ($reminder->user === null || $reminder->ticket === null) {
                continue;
            }

            if (
                $reminder->ticket->status === TicketStatus::Escalated
                && $reminder->ticket->childTickets->contains(
                    fn ($child) => ! in_array($child->status, [TicketStatus::Resolved, TicketStatus::Closed])
                )
            ) {
                continue;
            }

            $reminder->user->notify(new ReminderDueNotification($reminder));

            $reminder->update([
                'is_sent' => true,
                'sent_at' => now(),
            ]);

            $count++;
        }

        $this->info("Sent {$count} reminder notification(s).");

        return self::SUCCESS;
    }
}
