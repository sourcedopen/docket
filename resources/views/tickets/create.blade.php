<x-layouts.app title="New Ticket — {{ config('app.name') }}" page-title="New Ticket">
    <div class="space-y-4">
        @if ($parentTicketId)
            <div class="alert alert-info">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="h-6 w-6 shrink-0 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>Creating a follow-up for ticket #{{ $parentTicketId }}.</span>
            </div>
        @endif

        <p class="text-base-content/60">Select the ticket type to continue:</p>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($ticketTypes as $ticketType)
                <a href="{{ route('tickets.create-with-type', array_filter(['ticketType' => $ticketType->id, 'parent_ticket_id' => $parentTicketId])) }}" class="card bg-base-100 shadow hover:shadow-md hover:border-primary border border-base-200 transition-all">
                    <div class="card-body">
                        <h3 class="card-title text-base">{{ $ticketType->name }}</h3>
                        @if ($ticketType->description)
                            <p class="text-sm text-base-content/60">{{ Str::limit($ticketType->description, 100) }}</p>
                        @endif
                        @if ($ticketType->default_sla_days)
                            <div class="mt-2">
                                <span class="badge badge-outline badge-sm">SLA: {{ $ticketType->default_sla_days }} days</span>
                            </div>
                        @endif
                    </div>
                </a>
            @empty
                <div class="col-span-3 text-center text-base-content/50 py-12">
                    <p>No active ticket types found.</p>
                    <a href="{{ route('ticket-types.create') }}" class="btn btn-primary btn-sm mt-4">Create a Ticket Type</a>
                </div>
            @endforelse
        </div>
    </div>
</x-layouts.app>
