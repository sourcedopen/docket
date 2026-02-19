<?php

namespace App\Console\Commands;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Notifications\TicketOverdueNotification;
use Illuminate\Console\Command;

class CheckOverdueTickets extends Command
{
    protected $signature = 'tickets:check-overdue';

    protected $description = 'Send notifications for overdue tickets';

    public function handle(): int
    {
        $closedStatuses = [
            TicketStatus::Resolved->value,
            TicketStatus::Closed->value,
        ];

        $tickets = Ticket::query()
            ->with('user')
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', today())
            ->whereNotIn('status', $closedStatuses)
            ->whereNull('deleted_at')
            ->get();

        $count = 0;

        foreach ($tickets as $ticket) {
            if ($ticket->user === null) {
                continue;
            }

            $ticket->user->notify(new TicketOverdueNotification($ticket));
            $count++;
        }

        $this->info("Sent {$count} overdue ticket notification(s).");

        return self::SUCCESS;
    }
}
