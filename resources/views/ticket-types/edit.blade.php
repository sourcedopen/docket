<x-layouts.app title="Edit {{ $ticketType->name }} — {{ config('app.name') }}" page-title="Edit Ticket Type">
    <div class="max-w-3xl">
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <form method="POST" action="{{ route('ticket-types.update', $ticketType) }}">
                    @csrf
                    @method('PUT')

                    @include('ticket-types._form')

                    <div class="card-actions justify-end mt-4">
                        <a href="{{ route('ticket-types.index') }}" class="btn btn-ghost">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Ticket Type</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
