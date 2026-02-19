<?php

namespace App\Console\Commands;

use App\Models\Reminder;
use App\Notifications\ReminderDueNotification;
use Illuminate\Console\Command;

class SendReminders extends Command
{
    protected $signature = 'reminders:send';

    protected $description = 'Send notifications for due reminders';

    public function handle(): int
    {
        $reminders = Reminder::query()
            ->with(['ticket', 'user'])
            ->where('is_sent', false)
            ->where('remind_at', '<=', now())
            ->whereNull('deleted_at')
            ->get();

        $count = 0;

        foreach ($reminders as $reminder) {
            if ($reminder->user === null || $reminder->ticket === null) {
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
