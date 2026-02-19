<x-layouts.app title="Edit {{ $contact->name }} — {{ config('app.name') }}" page-title="Edit Contact">
    <div class="max-w-3xl">
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <form method="POST" action="{{ route('contacts.update', $contact) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    @include('contacts._form')

                    <div class="mt-4">
                        @include('partials._file_upload', ['label' => 'Documents', 'existingMedia' => $documents])
                    </div>

                    <div class="card-actions justify-end mt-4">
                        <a href="{{ route('contacts.show', $contact) }}" class="btn btn-ghost">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Contact</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
