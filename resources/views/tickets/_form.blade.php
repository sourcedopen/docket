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
            tags: @js(isset($ticket) ? $ticket->tags->pluck('name')->toArray() : []),
            addTag() {
                const trimmed = this.input.trim().replace(/,+$/, '').trim();
                if (trimmed && !this.tags.includes(trimmed)) {
                    this.tags.push(trimmed);
                }
                this.input = '';
            },
            removeTag(tag) {
                this.tags = this.tags.filter(t => t !== tag);
            },
            get tagsCsv() { return this.tags.join(','); }
        }"
    >
        <label class="label">
            <span class="label-text font-medium">Tags</span>
            <span class="label-text-alt">Press Enter or comma to add</span>
        </label>
        <input type="hidden" name="tags" :value="tagsCsv">
        <div class="flex flex-wrap gap-1 rounded-lg border border-base-300 p-2 min-h-[2.5rem] focus-within:border-primary">
            <template x-for="tag in tags" :key="tag">
                <span class="badge badge-primary gap-1">
                    <span x-text="tag"></span>
                    <button type="button" @click="removeTag(tag)" class="hover:text-error">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </span>
            </template>
            <input
                type="text"
                x-model="input"
                @keydown.enter.prevent="addTag()"
                @keydown.comma.prevent="addTag()"
                placeholder="{{ isset($ticket) && $ticket->tags->isEmpty() || ! isset($ticket) ? 'Add tags...' : '' }}"
                class="flex-1 min-w-[6rem] bg-transparent text-sm outline-none"
                list="tag-suggestions"
            >
        </div>
        <datalist id="tag-suggestions">
            @foreach ($allTags as $tag)
                <option value="{{ $tag->name }}">
            @endforeach
        </datalist>
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
