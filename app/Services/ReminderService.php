<?php

namespace App\Services;

use App\Enums\ReminderType;
use App\Models\Ticket;

class ReminderService
{
    /**
     * Auto-create deadline reminders for a ticket with a due_date.
     * Creates reminders at: 3 days before, 1 day before, and on the due date.
     */
    public function createDeadlineReminders(Ticket $ticket): void
    {
        if ($ticket->due_date === null || $ticket->user_id === null) {
            return;
        }

        $offsets = [
            ['days' => 3, 'label' => '3 days before deadline'],
            ['days' => 1, 'label' => '1 day before deadline'],
            ['days' => 0, 'label' => 'On deadline'],
        ];

        foreach ($offsets as $offset) {
            $remindAt = $ticket->due_date->copy()->subDays($offset['days'])->setTime(9, 0, 0);

            if ($remindAt->isPast()) {
                continue;
            }

            $ticket->reminders()->create([
                'user_id' => $ticket->user_id,
                'title' => "Deadline {$offset['label']}: {$ticket->reference_number}",
                'remind_at' => $remindAt,
                'type' => ReminderType::DeadlineApproaching->value,
                'is_sent' => false,
                'is_recurring' => false,
            ]);
        }
    }
}
