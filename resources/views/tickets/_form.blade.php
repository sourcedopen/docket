<div class="grid grid-cols-1 gap-6 md:grid-cols-2">
    {{-- Title --}}
    <div class="form-control md:col-span-2">
        <label class="label" for="title">
            <span class="label-text font-medium">Title <span class="text-error">*</span></span>
        </label>
        <input
            id="title"
            type="text"
            name="title"
            value="{{ old('title', $ticket->title ?? '') }}"
            class="input input-bordered @error('title') input-error @enderror"
            required
        >
        @error('title')
            <span class="label-text-alt text-error mt-1">{{ $message }}</span>
        @enderror
    </div>

    {{-- Status --}}
    <div class="form-control">
        <label class="label" for="status">
            <span class="label-text font-medium">Status <span class="text-error">*</span></span>
        </label>
        <select id="status" name="status" class="select select-bordered @error('status') select-error @enderror" required>
            @foreach (\App\Enums\TicketStatus::cases() as $status)
                <option value="{{ $status->value }}" {{ old('status', $ticket->status->value ?? \App\Enums\TicketStatus::Draft->value) === $status->value ? 'selected' : '' }}>
                    {{ $status->label() }}
                </option>
            @endforeach
        </select>
        @error('status')
            <span class="label-text-alt text-error mt-1">{{ $message }}</span>
        @enderror
    </div>

    {{-- Priority --}}
    <div class="form-control">
        <label class="label" for="priority">
            <span class="label-text font-medium">Priority <span class="text-error">*</span></span>
        </label>
        <select id="priority" name="priority" class="select select-bordered @error('priority') select-error @enderror" required>
            @foreach (\App\Enums\TicketPriority::cases() as $priority)
                <option value="{{ $priority->value }}" {{ old('priority', $ticket->priority->value ?? \App\Enums\TicketPriority::Medium->value) === $priority->value ? 'selected' : '' }}>
                    {{ $priority->label() }}
                </option>
            @endforeach
        </select>
        @error('priority')
            <span class="label-text-alt text-error mt-1">{{ $message }}</span>
        @enderror
    </div>

    {{-- Contact --}}
    <div class="form-control">
        <label class="label" for="filed_with_contact_id">
            <span class="label-text font-medium">Filed With</span>
        </label>
        <select id="filed_with_contact_id" name="filed_with_contact_id" class="select select-bordered @error('filed_with_contact_id') select-error @enderror">
            <option value="">None</option>
            @foreach ($contacts as $contact)
                <option value="{{ $contact->id }}" {{ old('filed_with_contact_id', $ticket->filed_with_contact_id ?? '') == $contact->id ? 'selected' : '' }}>
                    {{ $contact->name }}{{ $contact->organization ? ' — ' . $contact->organization : '' }}
                </option>
            @endforeach
        </select>
        @error('filed_with_contact_id')
            <span class="label-text-alt text-error mt-1">{{ $message }}</span>
        @enderror
    </div>

    {{-- External Reference --}}
    <div class="form-control">
        <label class="label" for="external_reference">
            <span class="label-text font-medium">External Reference</span>
        </label>
        <input
            id="external_reference"
            type="text"
            name="external_reference"
            value="{{ old('external_reference', $ticket->external_reference ?? '') }}"
            class="input input-bordered @error('external_reference') input-error @enderror"
        >
        @error('external_reference')
            <span class="label-text-alt text-error mt-1">{{ $message }}</span>
        @enderror
    </div>

    {{-- Filed Date --}}
    <div class="form-control">
        <label class="label" for="filed_date">
            <span class="label-text font-medium">Filed Date</span>
        </label>
        <input
            id="filed_date"
            type="date"
            name="filed_date"
            value="{{ old('filed_date', isset($ticket) ? $ticket->filed_date?->format('Y-m-d') : today()->format('Y-m-d')) }}"
            class="input input-bordered @error('filed_date') input-error @enderror"
        >
        @error('filed_date')
            <span class="label-text-alt text-error mt-1">{{ $message }}</span>
        @enderror
    </div>

    {{-- Due Date --}}
    <div class="form-control">
        <label class="label" for="due_date">
            <span class="label-text font-medium">Due Date</span>
        </label>
        <input
            id="due_date"
            type="date"
            name="due_date"
            value="{{ old('due_date', isset($ticket) ? $ticket->due_date?->format('Y-m-d') : '') }}"
            class="input input-bordered @error('due_date') input-error @enderror"
        >
        @error('due_date')
            <span class="label-text-alt text-error mt-1">{{ $message }}</span>
        @enderror
    </div>

    {{-- Tags --}}
    <div
        class="form-control md:col-span-2"
        x-data="{
            input: '',
            open: false,
            tags: @js(isset($ticket) ? $ticket->tags->pluck('name')->toArray() : []),
            allTags: @js($allTags->pluck('name')->toArray()),
            addTag(name) {
                const trimmed = (name ?? this.input).trim().replace(/,+$/, '').trim();
                if (trimmed && !this.tags.includes(trimmed)) {
                    this.tags.push(trimmed);
                }
                this.input = '';
            },
            removeTag(tag) {
                this.tags = this.tags.filter(t => t !== tag);
            },
            get tagsCsv() { return this.tags.join(','); },
            get availableTags() {
                const q = this.input.toLowerCase();
                return this.allTags.filter(t => !this.tags.includes(t) && (!q || t.toLowerCase().includes(q)));
            }
        }"
        @click.outside="open = false"
    >
        <label class="label">
            <span class="label-text font-medium">Tags</span>
            <span class="label-text-alt">Press Enter or comma to add</span>
        </label>
        <input type="hidden" name="tags" :value="tagsCsv">
        <div class="relative">
            <div
                class="flex flex-wrap gap-1 rounded-lg border border-base-300 p-2 min-h-[2.5rem] focus-within:border-primary cursor-text"
                @click="$refs.tagInput.focus(); open = true"
            >
                <template x-for="tag in tags" :key="tag">
                    <span class="inline-flex items-center gap-1 rounded-full bg-primary/15 px-3 py-1 text-sm text-primary">
                        <span x-text="tag"></span>
                        <button type="button" @click.stop="removeTag(tag)" class="cursor-pointer text-primary hover:text-error">&times;</button>
                    </span>
                </template>
                <input
                    type="text"
                    x-ref="tagInput"
                    x-model="input"
                    @focus="open = true"
                    @keydown.enter.prevent="addTag()"
                    @keydown.comma.prevent="addTag()"
                    :placeholder="tags.length === 0 ? 'Add tags...' : ''"
                    class="flex-1 min-w-[6rem] bg-transparent text-sm outline-none"
                >
            </div>
            <div
                x-show="open && availableTags.length > 0"
                x-cloak
                class="absolute z-10 mt-1 w-full rounded-lg border border-base-300 bg-base-100 p-3 shadow-lg"
            >
                <div class="flex flex-wrap gap-2">
                    <template x-for="tag in availableTags" :key="tag">
                        <button
                            type="button"
                            @click="addTag(tag); $refs.tagInput.focus()"
                            class="rounded-full border border-base-300 px-3 py-1 text-sm hover:bg-base-200 transition-colors"
                            x-text="tag"
                        ></button>
                    </template>
                </div>
            </div>
        </div>
    </div>

    {{-- Description --}}
    <div class="form-control md:col-span-2">
        <label class="label" for="description">
            <span class="label-text font-medium">Description</span>
        </label>
        <textarea
            id="description"
            name="description"
            rows="4"
            class="textarea textarea-bordered @error('description') textarea-error @enderror"
        >{{ old('description', $ticket->description ?? '') }}</textarea>
        @error('description')
            <span class="label-text-alt text-error mt-1">{{ $message }}</span>
        @enderror
    </div>
</div>
