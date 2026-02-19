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
            value="{{ old('name', $ticketType->name ?? '') }}"
            class="input input-bordered @error('name') input-error @enderror"
            required
        >
        @error('name')
            <span class="label-text-alt text-error mt-1">{{ $message }}</span>
        @enderror
    </div>

    {{-- Slug --}}
    <div class="form-control">
        <label class="label" for="slug">
            <span class="label-text font-medium">Slug</span>
            <span class="label-text-alt">Auto-generated from name if left blank</span>
        </label>
        <input
            id="slug"
            type="text"
            name="slug"
            value="{{ old('slug', $ticketType->slug ?? '') }}"
            class="input input-bordered @error('slug') input-error @enderror"
        >
        @error('slug')
            <span class="label-text-alt text-error mt-1">{{ $message }}</span>
        @enderror
    </div>

    {{-- Description --}}
    <div class="form-control md:col-span-2">
        <label class="label" for="description">
            <span class="label-text font-medium">Description</span>
        </label>
        <textarea
            id="description"
            name="description"
            rows="3"
            class="textarea textarea-bordered @error('description') textarea-error @enderror"
        >{{ old('description', $ticketType->description ?? '') }}</textarea>
        @error('description')
            <span class="label-text-alt text-error mt-1">{{ $message }}</span>
        @enderror
    </div>

    {{-- Default SLA Days --}}
    <div class="form-control">
        <label class="label" for="default_sla_days">
            <span class="label-text font-medium">Default SLA Days</span>
        </label>
        <input
            id="default_sla_days"
            type="number"
            name="default_sla_days"
            min="1"
            value="{{ old('default_sla_days', $ticketType->default_sla_days ?? '') }}"
            class="input input-bordered @error('default_sla_days') input-error @enderror"
        >
        @error('default_sla_days')
            <span class="label-text-alt text-error mt-1">{{ $message }}</span>
        @enderror
    </div>

    {{-- Sort Order --}}
    <div class="form-control">
        <label class="label" for="sort_order">
            <span class="label-text font-medium">Sort Order</span>
        </label>
        <input
            id="sort_order"
            type="number"
            name="sort_order"
            min="0"
            value="{{ old('sort_order', $ticketType->sort_order ?? 0) }}"
            class="input input-bordered @error('sort_order') input-error @enderror"
        >
        @error('sort_order')
            <span class="label-text-alt text-error mt-1">{{ $message }}</span>
        @enderror
    </div>

    {{-- Is Active --}}
    <div class="form-control">
        <label class="label cursor-pointer justify-start gap-4" for="is_active">
            <input
                id="is_active"
                type="checkbox"
                name="is_active"
                value="1"
                class="toggle toggle-primary"
                {{ old('is_active', $ticketType->is_active ?? true) ? 'checked' : '' }}
            >
            <span class="label-text font-medium">Active</span>
        </label>
    </div>

    {{-- Schema Definition --}}
    <div class="form-control md:col-span-2">
        <label class="label" for="schema_definition">
            <span class="label-text font-medium">Schema Definition</span>
            <span class="label-text-alt">JSON array of custom field definitions</span>
        </label>
        <textarea
            id="schema_definition"
            name="schema_definition"
            rows="10"
            class="textarea textarea-bordered font-mono text-sm @error('schema_definition') textarea-error @enderror"
            placeholder='[{"key": "case_number", "label": "Case Number", "type": "text", "required": true}]'
        >{{ old('schema_definition', isset($ticketType) && $ticketType->schema_definition ? json_encode($ticketType->schema_definition, JSON_PRETTY_PRINT) : '') }}</textarea>
        @error('schema_definition')
            <span class="label-text-alt text-error mt-1">{{ $message }}</span>
        @enderror
        <div class="label">
            <span class="label-text-alt">Supported types: text, number, date, select, textarea</span>
        </div>
    </div>
</div>
