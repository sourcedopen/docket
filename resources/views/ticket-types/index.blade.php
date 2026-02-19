<x-layouts.app title="Ticket Types — {{ config('app.name') }}" page-title="Ticket Types">
    <div class="space-y-4">
        <div class="flex justify-end">
            <a href="{{ route('ticket-types.create') }}" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                New Ticket Type
            </a>
        </div>

        <div class="card bg-base-100 shadow">
            <div class="card-body p-0">
                <div class="overflow-x-auto">
                    <table class="table table-zebra">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Slug</th>
                                <th>SLA Days</th>
                                <th>Status</th>
                                <th>Order</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($ticketTypes as $ticketType)
                                <tr>
                                    <td class="font-medium">{{ $ticketType->name }}</td>
                                    <td class="font-mono text-sm">{{ $ticketType->slug }}</td>
                                    <td>{{ $ticketType->default_sla_days ? $ticketType->default_sla_days . ' days' : '—' }}</td>
                                    <td>
                                        @if ($ticketType->is_active)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-ghost">Inactive</span>
                                        @endif
                                    </td>
                                    <td>{{ $ticketType->sort_order }}</td>
                                    <td class="text-right">
                                        <div class="join">
                                            <a href="{{ route('ticket-types.edit', $ticketType) }}" class="btn btn-sm btn-ghost join-item">
                                                Edit
                                            </a>
                                            <form method="POST" action="{{ route('ticket-types.destroy', $ticketType) }}" x-data @submit.prevent="if (confirm('Delete this ticket type?')) $el.submit()">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-ghost join-item text-error">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-base-content/50 py-8">No ticket types found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($ticketTypes->hasPages())
                    <div class="p-4 border-t border-base-200">
                        {{ $ticketTypes->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
