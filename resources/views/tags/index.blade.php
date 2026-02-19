<x-layouts.app title="Tags — {{ config('app.name') }}" page-title="Tags">
    <div class="space-y-4 max-w-2xl">
        {{-- Create tag form --}}
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <h2 class="card-title text-base">Add Tag</h2>
                <form method="POST" action="{{ route('tags.store') }}" class="flex gap-3">
                    @csrf
                    <div class="form-control flex-1">
                        <input
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="Tag name"
                            class="input input-bordered @error('name') input-error @enderror"
                            required
                        >
                        @error('name')
                            <span class="label-text-alt text-error mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">Add</button>
                </form>
            </div>
        </div>

        {{-- Tags list --}}
        <div class="card bg-base-100 shadow">
            <div class="card-body p-0">
                <div class="overflow-x-auto">
                    <table class="table table-zebra">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Slug</th>
                                <th>Tickets</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($tags as $tag)
                                <tr>
                                    <td>
                                        <a href="{{ route('tickets.index', ['tag' => $tag->name]) }}" class="badge badge-primary link link-hover">
                                            {{ $tag->name }}
                                        </a>
                                    </td>
                                    <td class="font-mono text-sm">{{ $tag->slug }}</td>
                                    <td>{{ $ticketCounts[$tag->id] ?? 0 }}</td>
                                    <td class="text-right">
                                        <form method="POST" action="{{ route('tags.destroy', $tag) }}" x-data @submit.prevent="if (confirm('Delete tag {{ addslashes($tag->name) }}?')) $el.submit()">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-ghost text-error">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-base-content/50 py-8">No tags yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
