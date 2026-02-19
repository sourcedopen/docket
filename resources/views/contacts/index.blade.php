<x-layouts.app title="Contacts — {{ config('app.name') }}" page-title="Contacts">
    <div class="space-y-4">
        <div class="flex justify-end">
            <a href="{{ route('contacts.create') }}" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                New Contact
            </a>
        </div>

        <div class="card bg-base-100 shadow">
            <div class="card-body p-0">
                <div class="overflow-x-auto">
                    <table class="table table-zebra">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Organization</th>
                                <th>Type</th>
                                <th>Email</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($contacts as $contact)
                                <tr>
                                    <td>
                                        <a href="{{ route('contacts.show', $contact) }}" class="font-medium link link-hover">
                                            {{ $contact->name }}
                                        </a>
                                        @if ($contact->designation)
                                            <div class="text-sm text-base-content/60">{{ $contact->designation }}</div>
                                        @endif
                                    </td>
                                    <td>{{ $contact->organization ?? '—' }}</td>
                                    <td>
                                        <span class="badge badge-outline">{{ $contact->type->label() }}</span>
                                    </td>
                                    <td>{{ $contact->email ?? '—' }}</td>
                                    <td class="text-right">
                                        <div class="join">
                                            <a href="{{ route('contacts.show', $contact) }}" class="btn btn-sm btn-ghost join-item">View</a>
                                            <a href="{{ route('contacts.edit', $contact) }}" class="btn btn-sm btn-ghost join-item">Edit</a>
                                            <form method="POST" action="{{ route('contacts.destroy', $contact) }}" x-data @submit.prevent="if (confirm('Delete this contact?')) $el.submit()">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-ghost join-item text-error">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-base-content/50 py-8">No contacts found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($contacts->hasPages())
                    <div class="p-4 border-t border-base-200">
                        {{ $contacts->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
