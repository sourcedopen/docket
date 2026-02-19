<x-layouts.app title="Tickets — {{ config('app.name') }}" page-title="Tickets">
    <div class="space-y-4">
        {{-- Filter bar + action --}}
        <div class="card bg-base-100 shadow">
            <div class="card-body py-3">
                <form method="GET" action="{{ route('tickets.index') }}" class="flex flex-wrap items-end gap-3">
                    <div class="form-control">
                        <label class="label py-1"><span class="label-text text-xs">Status</span></label>
                        <select name="status" class="select select-bordered select-sm">
                            <option value="">All Statuses</option>
                            @foreach (\App\Enums\TicketStatus::cases() as $status)
                                <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>
                                    {{ $status->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-control">
                        <label class="label py-1"><span class="label-text text-xs">Priority</span></label>
                        <select name="priority" class="select select-bordered select-sm">
                            <option value="">All Priorities</option>
                            @foreach (\App\Enums\TicketPriority::cases() as $priority)
                                <option value="{{ $priority->value }}" {{ request('priority') === $priority->value ? 'selected' : '' }}>
                                    {{ $priority->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-control">
                        <label class="label py-1"><span class="label-text text-xs">Type</span></label>
                        <select name="ticket_type_id" class="select select-bordered select-sm">
                            <option value="">All Types</option>
                            @foreach ($ticketTypes as $type)
                                <option value="{{ $type->id }}" {{ request('ticket_type_id') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn btn-sm btn-outline">Filter</button>
                    @if (request()->hasAny(['status', 'priority', 'ticket_type_id']))
                        <a href="{{ route('tickets.index') }}" class="btn btn-sm btn-ghost">Clear</a>
                    @endif

                    <div class="ml-auto">
                        <a href="{{ route('tickets.create') }}" class="btn btn-sm btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            New Ticket
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tickets table --}}
        <div class="card bg-base-100 shadow">
            <div class="card-body p-0">
                <div class="overflow-x-auto">
                    <table class="table table-zebra">
                        <thead>
                            <tr>
                                <th>Reference</th>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Priority</th>
                                <th>Filed With</th>
                                <th>Due</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($tickets as $ticket)
                                <tr>
                                    <td class="font-mono text-sm">
                                        <a href="{{ route('tickets.show', $ticket) }}" class="link link-hover">
                                            {{ $ticket->reference_number }}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{ route('tickets.show', $ticket) }}" class="link link-hover font-medium">
                                            {{ Str::limit($ticket->title, 50) }}
                                        </a>
                                    </td>
                                    <td>{{ $ticket->ticketType?->name ?? '—' }}</td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'draft' => 'badge-ghost',
                                                'submitted' => 'badge-info',
                                                'acknowledged' => 'badge-primary',
                                                'in_progress' => 'badge-warning',
                                                'escalated' => 'badge-error',
                                                'resolved' => 'badge-success',
                                                'closed' => 'badge-neutral',
                                                'reopened' => 'badge-secondary',
                                            ];
                                        @endphp
                                        <span class="badge badge-sm {{ $statusColors[$ticket->status->value] ?? 'badge-ghost' }}">
                                            {{ $ticket->status->label() }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $priorityColors = ['low' => 'badge-ghost', 'medium' => 'badge-info', 'high' => 'badge-warning', 'critical' => 'badge-error'];
                                        @endphp
                                        <span class="badge badge-sm {{ $priorityColors[$ticket->priority->value] ?? 'badge-ghost' }}">
                                            {{ $ticket->priority->label() }}
                                        </span>
                                    </td>
                                    <td>{{ $ticket->filedWithContact?->name ?? '—' }}</td>
                                    <td>{{ $ticket->due_date?->format('d M Y') ?? '—' }}</td>
                                    <td class="text-right">
                                        <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-sm btn-ghost">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-base-content/50 py-8">No tickets found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($tickets->hasPages())
                    <div class="p-4 border-t border-base-200">
                        {{ $tickets->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
