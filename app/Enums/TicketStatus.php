<?php

namespace App\Enums;

enum TicketStatus: string
{
    case Draft = 'draft';
    case Submitted = 'submitted';
    case Acknowledged = 'acknowledged';
    case InProgress = 'in_progress';
    case Escalated = 'escalated';
    case Resolved = 'resolved';
    case Closed = 'closed';
    case Reopened = 'reopened';

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'badge-ghost',
            self::Submitted => 'badge-info',
            self::Acknowledged => 'badge-primary',
            self::InProgress => 'badge-warning',
            self::Escalated => 'badge-error',
            self::Resolved => 'badge-success',
            self::Closed => 'badge-neutral',
            self::Reopened => 'badge-secondary',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Submitted => 'Submitted',
            self::Acknowledged => 'Acknowledged',
            self::InProgress => 'In Progress',
            self::Escalated => 'Escalated',
            self::Resolved => 'Resolved',
            self::Closed => 'Closed',
            self::Reopened => 'Reopened',
        };
    }
}
