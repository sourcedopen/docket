<?php

namespace App\Enums;

enum TicketPriority: string
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';
    case Critical = 'critical';

    public function color(): string
    {
        return match ($this) {
            self::Low => 'badge-ghost',
            self::Medium => 'badge-info',
            self::High => 'badge-warning',
            self::Critical => 'badge-error',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Low => 'Low',
            self::Medium => 'Medium',
            self::High => 'High',
            self::Critical => 'Critical',
        };
    }
}
