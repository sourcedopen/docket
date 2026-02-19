<?php

namespace App\Services;

use App\Enums\TicketStatus;

class TicketStateMachine
{
    /** @var array<string, TicketStatus[]> */
    private array $transitions = [];

    public function __construct()
    {
        $this->transitions = [
            TicketStatus::Draft->value => [TicketStatus::Submitted],
            TicketStatus::Submitted->value => [TicketStatus::Acknowledged, TicketStatus::InProgress, TicketStatus::Closed],
            TicketStatus::Acknowledged->value => [TicketStatus::InProgress, TicketStatus::Closed],
            TicketStatus::InProgress->value => [TicketStatus::Resolved, TicketStatus::Escalated, TicketStatus::Closed],
            TicketStatus::Escalated->value => [TicketStatus::Resolved, TicketStatus::Closed],
            TicketStatus::Resolved->value => [TicketStatus::Closed],
            TicketStatus::Closed->value => [TicketStatus::Reopened],
            TicketStatus::Reopened->value => [TicketStatus::InProgress, TicketStatus::Closed],
        ];
    }

    public function canTransition(TicketStatus $from, TicketStatus $to): bool
    {
        if ($from === $to) {
            return true;
        }

        $allowed = $this->transitions[$from->value] ?? [];

        foreach ($allowed as $allowedStatus) {
            if ($allowedStatus === $to) {
                return true;
            }
        }

        return false;
    }

    /** @return TicketStatus[] */
    public function allowedTransitions(TicketStatus $from): array
    {
        return $this->transitions[$from->value] ?? [];
    }
}
