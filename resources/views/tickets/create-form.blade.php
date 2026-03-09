<x-layouts.app title="New {{ $ticketType->name }} — {{ config('app.name') }}" page-title="New {{ $ticketType->name }}">
    <div class="max-w-3xl">
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data">
                    @csrf

                    <input type="hidden" name="ticket_type_id" value="{{ $ticketType->id }}">
                    @if ($parentTicketId)
                        <input type="hidden" name="parent_ticket_id" value="{{ $parentTicketId }}">
                    @endif

                    @include('tickets._form')

                    @if (! empty($schema))
                        <div class="divider">{{ $ticketType->name }} Fields</div>
                        @include('tickets._custom_fields', ['schema' => $schema, 'values' => old('custom_fields', [])])
                    @endif

                    <div class="divider">Cost Details (Optional)</div>
                    <div
                        x-data="{ addCost: {{ old('cost_amount') ? 'true' : 'false' }} }"
                        class="mb-4"
                    >
                        <label class="label cursor-pointer justify-start gap-2">
                            <input type="checkbox" class="checkbox checkbox-sm" x-model="addCost">
                            <span class="label-text">Add initial cost</span>
                        </label>

                        <div x-show="addCost" x-cloak class="grid grid-cols-1 gap-4 mt-4 md:grid-cols-2">
                            <div class="form-control">
                                <label class="label" for="cost_amount">
                                    <span class="label-text font-medium">Amount <span class="text-error">*</span></span>
                                </label>
                                <input
                                    id="cost_amount"
                                    type="number"
                                    name="cost_amount"
                                    step="0.01"
                                    min="0.01"
                                    value="{{ old('cost_amount') }}"
                                    class="input input-bordered @error('cost_amount') input-error @enderror"
                                >
                                @error('cost_amount')
                                    <span class="label-text-alt text-error mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-control">
                                <label class="label" for="cost_incurred_at">
                                    <span class="label-text font-medium">Incurred At <span class="text-error">*</span></span>
                                </label>
                                <input
                                    id="cost_incurred_at"
                                    type="date"
                                    name="cost_incurred_at"
                                    value="{{ old('cost_incurred_at', today()->format('Y-m-d')) }}"
                                    class="input input-bordered @error('cost_incurred_at') input-error @enderror"
                                >
                                @error('cost_incurred_at')
                                    <span class="label-text-alt text-error mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-control md:col-span-2">
                                <label class="label" for="cost_description">
                                    <span class="label-text font-medium">Description</span>
                                </label>
                                <input
                                    id="cost_description"
                                    type="text"
                                    name="cost_description"
                                    value="{{ old('cost_description') }}"
                                    class="input input-bordered @error('cost_description') input-error @enderror"
                                >
                                @error('cost_description')
                                    <span class="label-text-alt text-error mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        @include('partials._file_upload', ['label' => 'Documents', 'existingMedia' => collect()])
                    </div>

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
