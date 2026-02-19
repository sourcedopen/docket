<x-layouts.app title="New {{ $ticketType->name }} — {{ config('app.name') }}" page-title="New {{ $ticketType->name }}">
    <div class="max-w-3xl">
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <form method="POST" action="{{ route('tickets.store') }}">
                    @csrf

                    <input type="hidden" name="ticket_type_id" value="{{ $ticketType->id }}">

                    @include('tickets._form')

                    @if (! empty($schema))
                        <div class="divider">{{ $ticketType->name }} Fields</div>
                        @include('tickets._custom_fields', ['schema' => $schema, 'values' => old('custom_fields', [])])
                    @endif

                    <div class="card-actions justify-between mt-6">
                        <a href="{{ route('tickets.create') }}" class="btn btn-ghost">Back</a>
                        <div class="flex gap-2">
                            <a href="{{ route('tickets.index') }}" class="btn btn-ghost">Cancel</a>
                            <button type="submit" class="btn btn-primary">Create Ticket</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
