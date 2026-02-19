<x-layouts.app title="Dashboard — {{ config('app.name') }}" page-title="Dashboard">
    <div class="space-y-6">

        {{-- Stat cards --}}
        <div class="stats stats-vertical sm:stats-horizontal shadow w-full">
            <div class="stat">
                <div class="stat-title">Open Tickets</div>
                <div class="stat-value text-primary">{{ $openCount }}</div>
                <div class="stat-desc">
                    <a href="{{ route('tickets.index') }}" class="link link-hover">View all</a>
                </div>
            </div>

            <div class="stat">
                <div class="stat-title">Overdue</div>
                <div class="stat-value {{ $overdueCount > 0 ? 'text-error' : 'text-base-content' }}">{{ $overdueCount }}</div>
                <div class="stat-desc">
                    <a href="{{ route('tickets.index', ['overdue' => '1']) }}" class="link link-hover">View overdue</a>
                </div>
            </div>

            <div class="stat">
                <div class="stat-title">Due This Week</div>
                <div class="stat-value {{ $dueSoonCount > 0 ? 'text-warning' : 'text-base-content' }}">{{ $dueSoonCount }}</div>
                <div class="stat-desc">Next 7 days</div>
            </div>

            <div class="stat">
                <div class="stat-title">Closed (30 days)</div>
                <div class="stat-value text-success">{{ $recentlyClosedCount }}</div>
                <div class="stat-desc">Recently resolved</div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

            {{-- Overdue tickets --}}
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <h2 class="card-title text-base text-error">Overdue Tickets</h2>
                        @if ($overdueCount > 10)
                            <a href="{{ route('tickets.index', ['overdue' => '1']) }}" class="text-sm link link-hover">View all {{ $overdueCount }}</a>
                        @endif
                    </div>

                    @forelse ($overdueTickets as $ticket)
                        <div class="flex items-start justify-between gap-2 py-2 border-b border-base-200 last:border-0">
                            <div class="min-w-0">
                                <a href="{{ route('tickets.show', $ticket) }}" class="link link-hover font-medium text-sm block truncate">
                                    {{ $ticket->title }}
                                </a>
                                <div class="text-xs text-base-content/50 mt-0.5">
                                    <span class="font-mono">{{ $ticket->reference_number }}</span>
                                    @if ($ticket->ticketType)
                                        · {{ $ticket->ticketType->name }}
                                    @endif
                                </div>
                            </div>
                            <div class="shrink-0 text-right">
                                <div class="text-xs text-error font-medium">
                                    {{ now()->diffInDays($ticket->due_date) }}d overdue
                                </div>
                                <div class="text-xs text-base-content/40">
                                    {{ $ticket->due_date->format('d M') }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-base-content/50 py-4 text-center">No overdue tickets.</p>
                    @endforelse
                </div>
            </div>

            {{-- Upcoming reminders --}}
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h2 class="card-title text-base">Upcoming Reminders</h2>
                    <p class="text-xs text-base-content/50 -mt-2">Next 7 days</p>

                    @forelse ($upcomingReminders as $reminder)
                        <div class="flex items-start justify-between gap-2 py-2 border-b border-base-200 last:border-0">
                            <div class="min-w-0">
                                <div class="font-medium text-sm truncate">{{ $reminder->title }}</div>
                                @if ($reminder->ticket)
                                    <a href="{{ route('tickets.show', $reminder->ticket) }}" class="text-xs link link-hover text-base-content/50">
                                        {{ $reminder->ticket->reference_number }}
                                    </a>
                                @endif
                            </div>
                            <div class="shrink-0 text-right">
                                <div class="text-xs font-medium">{{ $reminder->remind_at->format('d M') }}</div>
                                <div class="text-xs text-base-content/40">{{ $reminder->remind_at->format('H:i') }}</div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-base-content/50 py-4 text-center">No upcoming reminders.</p>
                    @endforelse
                </div>
            </div>

            {{-- Recent activity --}}
            <div class="card bg-base-100 shadow lg:col-span-2">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <h2 class="card-title text-base">Recent Activity</h2>
                        <a href="{{ route('activity.index') }}" class="text-sm link link-hover">View all</a>
                    </div>

                    @forelse ($recentActivity as $activity)
                        @php
                            $eventColors = ['created' => 'badge-success', 'updated' => 'badge-info', 'deleted' => 'badge-error'];
                            $subjectLabel = $activity->subject?->reference_number
                                ?? $activity->subject?->title
                                ?? $activity->subject?->name
                                ?? class_basename($activity->subject_type) . ' #' . $activity->subject_id;
                        @endphp
                        <div class="flex items-center gap-3 py-2 border-b border-base-200 last:border-0">
                            <span class="badge badge-xs {{ $eventColors[$activity->event] ?? 'badge-ghost' }} shrink-0">{{ $activity->event }}</span>
                            <div class="min-w-0 flex-1">
                                <span class="text-sm font-medium">{{ $activity->causer?->name ?? 'System' }}</span>
                                <span class="text-sm text-base-content/60 ml-1">{{ $activity->event }} </span>
                                @if ($activity->subject instanceof \App\Models\Ticket)
                                    <a href="{{ route('tickets.show', $activity->subject) }}" class="text-sm link link-hover">{{ $subjectLabel }}</a>
                                @elseif ($activity->subject instanceof \App\Models\Contact)
                                    <a href="{{ route('contacts.show', $activity->subject) }}" class="text-sm link link-hover">{{ $subjectLabel }}</a>
                                @else
                                    <span class="text-sm">{{ $subjectLabel }}</span>
                                @endif
                            </div>
                            <span class="text-xs text-base-content/40 shrink-0">{{ $activity->created_at->diffForHumans() }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-base-content/50 py-4 text-center">No recent activity.</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-layouts.app>
