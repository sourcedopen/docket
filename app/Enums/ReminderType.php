<?php

namespace App\Enums;

enum ReminderType: string
{
    case DeadlineApproaching = 'deadline_approaching';
    case FollowUp = 'follow_up';
    case Custom = 'custom';

    public function label(): string
    {
        return match ($this) {
            self::DeadlineApproaching => 'Deadline Approaching',
            self::FollowUp => 'Follow Up',
            self::Custom => 'Custom',
        };
    }
}
