<?php

namespace App\Services;

use App\Models\Ticket;

class ReferenceNumberGenerator
{
    public function generate(Ticket $ticket): string
    {
        $year = ($ticket->filed_date ?? now())->year;
        $prefix = "TKT-{$year}-";

        $lastRef = Ticket::withTrashed()
            ->where('reference_number', 'like', $prefix.'%')
            ->orderByDesc('reference_number')
            ->value('reference_number');

        $nextNumber = $lastRef ? ((int) substr($lastRef, strlen($prefix))) + 1 : 1;

        return sprintf('TKT-%d-%04d', $year, $nextNumber);
    }
}
