<x-layouts.app title="New Contact — {{ config('app.name') }}" page-title="New Contact">
    <div class="max-w-3xl">
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <form method="POST" action="{{ route('contacts.store') }}" enctype="multipart/form-data">
                    @csrf

                    @include('contacts._form')

                    <div class="mt-4">
                        @include('partials._file_upload', ['label' => 'Documents', 'existingMedia' => collect()])
                    </div>

                    <div class="card-actions justify-end mt-4">
                        <a href="{{ route('contacts.index') }}" class="btn btn-ghost">Cancel</a>
                        <button type="submit" class="btn btn-primary">Create Contact</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
