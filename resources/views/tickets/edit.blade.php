<x-layouts.app title="Edit {{ $ticket->reference_number }} — {{ config('app.name') }}" page-title="Edit Ticket">
    <div class="max-w-3xl">
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <form method="POST" action="{{ route('tickets.update', $ticket) }}">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="ticket_type_id" value="{{ $ticket->ticket_type_id }}">

                    @include('tickets._form')

                    @if (! empty($schema))
                        <div class="divider">{{ $ticket->ticketType?->name }} Fields</div>
                        @include('tickets._custom_fields', ['schema' => $schema, 'values' => old('custom_fields', $ticket->custom_fields ?? [])])
                    @endif

                    <div class="card-actions justify-between mt-6">
                        <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-ghost">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Ticket</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
