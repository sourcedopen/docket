<?php

namespace App\Observers;

use App\Models\Ticket;
use App\Services\ReferenceNumberGenerator;
use App\Services\ReminderService;

class TicketObserver
{
    public function __construct(
        private readonly ReferenceNumberGenerator $referenceNumberGenerator,
        private readonly ReminderService $reminderService,
    ) {}

    public function creating(Ticket $ticket): void
    {
        if (empty($ticket->reference_number)) {
            $ticket->reference_number = $this->referenceNumberGenerator->generate();
        }

        if ($ticket->due_date === null && $ticket->filed_date !== null) {
            $slaDays = $ticket->ticketType?->default_sla_days;

            if ($slaDays !== null) {
                $ticket->due_date = $ticket->filed_date->addDays($slaDays);
            }
        }
    }

    public function created(Ticket $ticket): void
    {
        if ($ticket->due_date !== null) {
            $this->reminderService->createDeadlineReminders($ticket);
        }
    }
}
