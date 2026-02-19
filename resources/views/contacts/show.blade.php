<x-layouts.app title="{{ $contact->name }} — {{ config('app.name') }}" page-title="{{ $contact->name }}">
    <div class="space-y-6">
        {{-- Header actions --}}
        <div class="flex justify-end gap-2">
            <a href="{{ route('contacts.edit', $contact) }}" class="btn btn-outline btn-sm">Edit</a>
            <form method="POST" action="{{ route('contacts.destroy', $contact) }}" x-data @submit.prevent="if (confirm('Delete this contact?')) $el.submit()">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-error btn-outline btn-sm">Delete</button>
            </form>
        </div>

        {{-- Contact details card --}}
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="text-xl font-bold">{{ $contact->name }}</h2>
                        @if ($contact->designation)
                            <p class="text-base-content/60">{{ $contact->designation }}</p>
                        @endif
                    </div>
                    <span class="badge badge-outline badge-lg">{{ $contact->type->label() }}</span>
                </div>

                <div class="divider my-2"></div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    @if ($contact->organization)
                        <div>
                            <div class="text-sm font-medium text-base-content/60">Organization</div>
                            <div>{{ $contact->organization }}</div>
                        </div>
                    @endif

                    @if ($contact->email)
                        <div>
                            <div class="text-sm font-medium text-base-content/60">Email</div>
                            <a href="mailto:{{ $contact->email }}" class="link link-primary">{{ $contact->email }}</a>
                        </div>
                    @endif

                    @if ($contact->phone)
                        <div>
                            <div class="text-sm font-medium text-base-content/60">Phone</div>
                            <div>{{ $contact->phone }}</div>
                        </div>
                    @endif

                    @if ($contact->address)
                        <div class="sm:col-span-2">
                            <div class="text-sm font-medium text-base-content/60">Address</div>
                            <div class="whitespace-pre-line">{{ $contact->address }}</div>
                        </div>
                    @endif

                    @if ($contact->notes)
                        <div class="sm:col-span-2">
                            <div class="text-sm font-medium text-base-content/60">Notes</div>
                            <div class="whitespace-pre-line">{{ $contact->notes }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Documents --}}
        @if ($documents->isNotEmpty())
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h3 class="card-title text-lg">Documents</h3>
                    <div class="space-y-1">
                        @foreach ($documents as $media)
                            <div class="flex items-center justify-between rounded-lg border border-base-200 px-3 py-2 text-sm">
                                <a href="{{ $media->getUrl() }}" target="_blank" class="link link-hover truncate">
                                    {{ $media->file_name }}
                                    <span class="text-base-content/40 ml-2">{{ number_format($media->size / 1024, 1) }} KB</span>
                                </a>
                                <form method="POST" action="{{ route('media.destroy', $media) }}" x-data @submit.prevent="if(confirm('Delete this file?')) $el.submit()">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-ghost text-error ml-2">Delete</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- Related tickets --}}
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <h3 class="card-title text-lg">Related Tickets</h3>

                <div class="overflow-x-auto">
                    <table class="table table-zebra">
                        <thead>
                            <tr>
                                <th>Reference</th>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Filed</th>
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
                                    <td>{{ $ticket->title }}</td>
                                    <td>{{ $ticket->ticketType?->name ?? '—' }}</td>
                                    <td>
                                        <span class="badge badge-sm">{{ $ticket->status->label() }}</span>
                                    </td>
                                    <td>{{ $ticket->filed_date?->format('d M Y') ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-base-content/50 py-4">No tickets filed with this contact.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($tickets->hasPages())
                    <div class="mt-2">
                        {{ $tickets->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
