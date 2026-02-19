<?php

namespace App\Http\Controllers;

use App\Enums\TicketStatus;
use App\Models\Reminder;
use App\Models\Ticket;
use Illuminate\View\View;
use Spatie\Activitylog\Models\Activity;

class DashboardController extends Controller
{
    public function index(): View
    {
        $userId = auth()->id();

        $closedStatuses = [TicketStatus::Resolved->value, TicketStatus::Closed->value];
        $openStatuses = array_map(
            fn ($s) => $s->value,
            array_filter(TicketStatus::cases(), fn ($s) => ! in_array($s->value, $closedStatuses))
        );

        $openCount = Ticket::query()
            ->where('user_id', $userId)
            ->whereIn('status', $openStatuses)
            ->count();

        $overdueCount = Ticket::query()
            ->where('user_id', $userId)
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', today())
            ->whereNotIn('status', $closedStatuses)
            ->count();

        $dueSoonCount = Ticket::query()
            ->where('user_id', $userId)
            ->whereNotNull('due_date')
            ->whereBetween('due_date', [today(), today()->addDays(7)])
            ->whereNotIn('status', $closedStatuses)
            ->count();

        $recentlyClosedCount = Ticket::query()
            ->where('user_id', $userId)
            ->whereIn('status', $closedStatuses)
            ->where('updated_at', '>=', now()->subDays(30))
            ->count();

        $overdueTickets = Ticket::query()
            ->with(['ticketType', 'filedWithContact'])
            ->where('user_id', $userId)
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', today())
            ->whereNotIn('status', $closedStatuses)
            ->orderBy('due_date')
            ->limit(10)
            ->get();

        $upcomingReminders = Reminder::query()
            ->with('ticket')
            ->where('user_id', $userId)
            ->where('is_sent', false)
            ->whereBetween('remind_at', [now(), now()->addDays(7)])
            ->orderBy('remind_at')
            ->limit(10)
            ->get();

        $recentActivity = Activity::query()
            ->with(['subject', 'causer'])
            ->latest()
            ->limit(10)
            ->get();

        return view('dashboard', compact(
            'openCount',
            'overdueCount',
            'dueSoonCount',
            'recentlyClosedCount',
            'overdueTickets',
            'upcomingReminders',
            'recentActivity',
        ));
    }
}
