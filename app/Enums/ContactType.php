<?php

namespace App\Enums;

enum ContactType: string
{
    case Authority = 'authority';
    case Company = 'company';
    case Department = 'department';
    case Individual = 'individual';

    public function label(): string
    {
        return match ($this) {
            self::Authority => 'Authority',
            self::Company => 'Company',
            self::Department => 'Department',
            self::Individual => 'Individual',
        };
    }
}
