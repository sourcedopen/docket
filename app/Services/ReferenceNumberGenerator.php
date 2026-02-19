<?php

namespace App\Services;

use App\Models\Ticket;

class ReferenceNumberGenerator
{
    public function generate(): string
    {
        $year = now()->year;
        $count = Ticket::query()->whereYear('created_at', $year)->count() + 1;

        return sprintf('TKT-%d-%04d', $year, $count);
    }
}
