<div class="grid grid-cols-1 gap-6 md:grid-cols-2">
    {{-- Name --}}
    <div class="form-control">
        <label class="label" for="name">
            <span class="label-text font-medium">Name <span class="text-error">*</span></span>
        </label>
        <input
            id="name"
            type="text"
            name="name"
            value="{{ old('name', $contact->name ?? '') }}"
            class="input input-bordered @error('name') input-error @enderror"
            required
        >
        @error('name')
            <span class="label-text-alt text-error mt-1">{{ $message }}</span>
        @enderror
    </div>

    {{-- Type --}}
    <div class="form-control">
        <label class="label" for="type">
            <span class="label-text font-medium">Type <span class="text-error">*</span></span>
        </label>
        <select id="type" name="type" class="select select-bordered @error('type') select-error @enderror" required>
            <option value="">Select type</option>
            @foreach (\App\Enums\ContactType::cases() as $type)
                <option value="{{ $type->value }}" {{ old('type', $contact->type->value ?? '') === $type->value ? 'selected' : '' }}>
                    {{ $type->label() }}
                </option>
            @endforeach
        </select>
        @error('type')
            <span class="label-text-alt text-error mt-1">{{ $message }}</span>
        @enderror
    </div>

    {{-- Designation --}}
    <div class="form-control">
        <label class="label" for="designation">
            <span class="label-text font-medium">Designation</span>
        </label>
        <input
            id="designation"
            type="text"
            name="designation"
            value="{{ old('designation', $contact->designation ?? '') }}"
            class="input input-bordered @error('designation') input-error @enderror"
        >
        @error('designation')
            <span class="label-text-alt text-error mt-1">{{ $message }}</span>
        @enderror
    </div>

    {{-- Organization --}}
    <div class="form-control">
        <label class="label" for="organization">
            <span class="label-text font-medium">Organization</span>
        </label>
        <input
            id="organization"
            type="text"
            name="organization"
            value="{{ old('organization', $contact->organization ?? '') }}"
            class="input input-bordered @error('organization') input-error @enderror"
        >
        @error('organization')
            <span class="label-text-alt text-error mt-1">{{ $message }}</span>
        @enderror
    </div>

    {{-- Email --}}
    <div class="form-control">
        <label class="label" for="email">
            <span class="label-text font-medium">Email</span>
        </label>
        <input
            id="email"
            type="email"
            name="email"
            value="{{ old('email', $contact->email ?? '') }}"
            class="input input-bordered @error('email') input-error @enderror"
        >
        @error('email')
            <span class="label-text-alt text-error mt-1">{{ $message }}</span>
        @enderror
    </div>

    {{-- Phone --}}
    <div class="form-control">
        <label class="label" for="phone">
            <span class="label-text font-medium">Phone</span>
        </label>
        <input
            id="phone"
            type="text"
            name="phone"
            value="{{ old('phone', $contact->phone ?? '') }}"
            class="input input-bordered @error('phone') input-error @enderror"
        >
        @error('phone')
            <span class="label-text-alt text-error mt-1">{{ $message }}</span>
        @enderror
    </div>

    {{-- Address --}}
    <div class="form-control md:col-span-2">
        <label class="label" for="address">
            <span class="label-text font-medium">Address</span>
        </label>
        <textarea
            id="address"
            name="address"
            rows="2"
            class="textarea textarea-bordered @error('address') textarea-error @enderror"
        >{{ old('address', $contact->address ?? '') }}</textarea>
        @error('address')
            <span class="label-text-alt text-error mt-1">{{ $message }}</span>
        @enderror
    </div>

    {{-- Notes --}}
    <div class="form-control md:col-span-2">
        <label class="label" for="notes">
            <span class="label-text font-medium">Notes</span>
        </label>
        <textarea
            id="notes"
            name="notes"
            rows="3"
            class="textarea textarea-bordered @error('notes') textarea-error @enderror"
        >{{ old('notes', $contact->notes ?? '') }}</textarea>
        @error('notes')
            <span class="label-text-alt text-error mt-1">{{ $message }}</span>
        @enderror
    </div>
</div>
