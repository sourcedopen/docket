<x-layouts.app title="{{ $ticket->reference_number }} — {{ config('app.name') }}" page-title="{{ $ticket->reference_number }}">
    <div class="space-y-4">
        {{-- Header --}}
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div class="space-y-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="font-mono text-sm text-base-content/60">{{ $ticket->reference_number }}</span>
                            <span class="badge {{ $ticket->status->color() }}">
                                {{ $ticket->status->label() }}
                            </span>
                            <span class="badge {{ $ticket->priority->color() }}">
                                {{ $ticket->priority->label() }}
                            </span>
                        </div>
                        <h1 class="text-xl font-bold">{{ $ticket->title }}</h1>
                        <div class="flex flex-wrap items-center gap-2 text-sm text-base-content/60">
                            <span>{{ $ticket->ticketType?->name ?? 'Unknown Type' }}</span>
                            @if ($ticket->filedWithContact)
                                <span>·</span>
                                <span>Filed with <a href="{{ route('contacts.show', $ticket->filedWithContact) }}" class="link">{{ $ticket->filedWithContact->name }}</a></span>
                            @endif
                            @foreach ($ticket->tags as $tag)
                                <a href="{{ route('tickets.index', ['tag' => $tag->name]) }}" class="badge badge-sm" style="background-color: {{ $tag->color ?? '#6b7280' }}; color: #fff; border-color: rgba(255,255,255,0.25);">{{ $tag->name }}</a>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        {{-- Status change dropdown --}}
                        @if ($allowedStatuses)
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" type="button" class="btn btn-sm btn-outline">
                                    Change Status
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div x-show="open" @click.outside="open = false" class="absolute right-0 mt-1 w-48 bg-base-100 shadow-lg rounded-box border border-base-200 z-50" x-cloak>
                                    <ul class="menu menu-sm p-2">
                                        @foreach ($allowedStatuses as $status)
                                            <li>
                                                <form method="POST" action="{{ route('tickets.update', $ticket) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="title" value="{{ $ticket->title }}">
                                                    <input type="hidden" name="ticket_type_id" value="{{ $ticket->ticket_type_id }}">
                                                    <input type="hidden" name="status" value="{{ $status->value }}">
                                                    <input type="hidden" name="priority" value="{{ $ticket->priority->value }}">
                                                    <button type="submit" class="w-full text-left">{{ $status->label() }}</button>
                                                </form>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif

                        <a href="{{ route('tickets.create', ['parent_ticket_id' => $ticket->id]) }}" class="btn btn-sm btn-outline">Create Follow-Up</a>
                        <a href="{{ route('tickets.edit', $ticket) }}" class="btn btn-sm btn-outline">Edit</a>

                        <form method="POST" action="{{ route('tickets.destroy', $ticket) }}" x-data @submit.prevent="if (confirm('Delete this ticket?')) $el.submit()">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-error btn-outline">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabs --}}
        <div x-data="{ tab: 'details' }">
            <div role="tablist" class="tabs tabs-bordered">
                <a role="tab" @click="tab = 'details'" :class="{ 'tab-active': tab === 'details' }" class="tab">Details</a>
                <a role="tab" @click="tab = 'timeline'" :class="{ 'tab-active': tab === 'timeline' }" class="tab">
                    Timeline
                    <span class="badge badge-sm ml-1">{{ $comments->count() }}</span>
                </a>
                <a role="tab" @click="tab = 'reminders'" :class="{ 'tab-active': tab === 'reminders' }" class="tab">
                    Reminders
                    <span class="badge badge-sm ml-1">{{ $ticket->reminders->count() }}</span>
                </a>
            </div>

            {{-- Details tab --}}
            <div x-show="tab === 'details'" class="mt-4">
                <div class="card bg-base-100 shadow">
                    <div class="card-body">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <div class="text-sm font-medium text-base-content/60">Filed Date</div>
                                <div>{{ $ticket->filed_date?->format('d M Y') ?? '—' }}</div>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-base-content/60">Due Date</div>
                                <div>{{ $ticket->due_date?->format('d M Y') ?? '—' }}</div>
                            </div>
                            @if ($ticket->closed_date)
                                <div>
                                    <div class="text-sm font-medium text-base-content/60">Closed Date</div>
                                    <div>{{ $ticket->closed_date->format('d M Y') }}</div>
                                </div>
                            @endif
                            @if ($ticket->external_reference)
                                <div>
                                    <div class="text-sm font-medium text-base-content/60">External Reference</div>
                                    <div class="font-mono">{{ $ticket->external_reference }}</div>
                                </div>
                            @endif
                            <div>
                                <div class="text-sm font-medium text-base-content/60">Created By</div>
                                <div>{{ $ticket->user?->name ?? '—' }}</div>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-base-content/60">Created At</div>
                                <div>{{ $ticket->created_at->format('d M Y H:i') }}</div>
                            </div>
                        </div>

                        @if ($ticket->description)
                            <div class="divider"></div>
                            <div>
                                <div class="text-sm font-medium text-base-content/60 mb-2">Description</div>
                                <div class="prose max-w-none">{{ $ticket->description }}</div>
                            </div>
                        @endif

                        @if ($ticket->custom_fields && count($ticket->custom_fields) > 0)
                            <div class="divider"></div>
                            <div>
                                <div class="text-sm font-medium text-base-content/60 mb-3">Custom Fields</div>
                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    @foreach ($ticket->custom_fields as $key => $value)
                                        @if ($value !== null && $value !== '')
                                            <div>
                                                <div class="text-sm font-medium text-base-content/60">{{ ucwords(str_replace('_', ' ', $key)) }}</div>
                                                <div>{{ $value }}</div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if ($ticket->parentTicket || $ticket->childTickets->isNotEmpty())
                            <div class="divider"></div>
                            <div>
                                <div class="text-sm font-medium text-base-content/60 mb-3">Linked Tickets</div>
                                @if ($ticket->parentTicket)
                                    <div class="mb-2">
                                        <span class="text-xs text-base-content/50 uppercase tracking-wide">Parent</span>
                                        <div class="mt-1">
                                            <a href="{{ route('tickets.show', $ticket->parentTicket) }}" class="link link-hover text-sm font-mono">{{ $ticket->parentTicket->reference_number }}</a>
                                            <span class="text-sm text-base-content/60 ml-2">{{ $ticket->parentTicket->title }}</span>
                                        </div>
                                    </div>
                                @endif
                                @if ($ticket->childTickets->isNotEmpty())
                                    <div>
                                        <span class="text-xs text-base-content/50 uppercase tracking-wide">Follow-Ups</span>
                                        <div class="mt-1 space-y-1">
                                            @foreach ($ticket->childTickets as $child)
                                                <div class="flex items-center gap-2 text-sm">
                                                    <a href="{{ route('tickets.show', $child) }}" class="link link-hover font-mono">{{ $child->reference_number }}</a>
                                                    <span class="badge badge-xs {{ $child->status->color() }}">{{ $child->status->label() }}</span>
                                                    <span class="text-base-content/60 truncate">{{ $child->title }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Timeline tab --}}
            <div x-show="tab === 'timeline'" class="mt-4 space-y-4">
                {{-- Add comment form --}}
                <div class="card bg-base-100 shadow">
                    <div class="card-body">
                        <h3 class="font-medium mb-3">Add Comment</h3>
                        <form method="POST" action="{{ route('tickets.comments.store', $ticket) }}" enctype="multipart/form-data">
                            @csrf

                            <div class="form-control mb-4">
                                <select name="type" class="select select-bordered select-sm @error('type') select-error @enderror" required>
                                    @foreach (\App\Enums\CommentType::cases() as $type)
                                        <option value="{{ $type->value }}" {{ old('type') === $type->value ? 'selected' : '' }}>
                                            {{ $type->label() }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('type')
                                    <span class="label-text-alt text-error mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-control mb-4">
                                <textarea
                                    name="body"
                                    rows="3"
                                    class="textarea textarea-bordered @error('body') textarea-error @enderror"
                                    placeholder="Write your comment..."
                                    required
                                >{{ old('body') }}</textarea>
                                @error('body')
                                    <span class="label-text-alt text-error mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-control mb-4">
                                <label class="label"><span class="label-text text-sm">Date &amp; Time</span></label>
                                <input
                                    type="datetime-local"
                                    name="commented_at"
                                    value="{{ old('commented_at', now()->format('Y-m-d\TH:i')) }}"
                                    class="input input-bordered input-sm @error('commented_at') input-error @enderror"
                                    required
                                >
                                @error('commented_at')
                                    <span class="label-text-alt text-error mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-control mb-4">
                                <label class="label"><span class="label-text text-sm">Attachments</span></label>
                                <input type="file" name="files[]" multiple class="file-input file-input-bordered file-input-sm w-full">
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" class="btn btn-primary btn-sm">Add Comment</button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Ticket-level documents --}}
                @if ($documents->isNotEmpty())
                    <div class="card bg-base-100 shadow">
                        <div class="card-body py-3">
                            <div class="text-sm font-medium text-base-content/60 mb-2">Attached Documents</div>
                            <x-attachment-list :media="$documents" />
                        </div>
                    </div>
                @endif

                {{-- Comments --}}
                @forelse ($comments as $comment)
                    <div x-data="{ expanded: false }" class="card bg-base-100 shadow">
                        <div class="card-body py-3">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex items-center gap-2">
                                    <div class="font-medium text-sm">{{ $comment->user?->name ?? 'Unknown' }}</div>
                                    <span class="badge badge-outline badge-xs">{{ $comment->type->label() }}</span>
                                    <span class="text-xs text-base-content/50">{{ $comment->commented_at->diffForHumans() }}</span>
                                </div>
                                <form
                                    method="POST"
                                    action="{{ route('tickets.comments.destroy', [$ticket, $comment]) }}"
                                    x-data
                                    @submit.prevent="if (confirm('Delete this comment?')) $el.submit()"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-ghost text-error">Delete</button>
                                </form>
                            </div>
                            <div class="mt-1 text-sm whitespace-pre-line">{{ $comment->body }}</div>
                            @php $commentMedia = $comment->getMedia('attachments'); @endphp
                            @if ($commentMedia->isNotEmpty())
                                <div class="mt-2">
                                    <button
                                        type="button"
                                        @click="expanded = !expanded"
                                        class="btn btn-xs btn-ghost gap-1"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 transition-transform" :class="{ 'rotate-90': expanded }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                        {{ $commentMedia->count() }} {{ Str::plural('attachment', $commentMedia->count()) }}
                                    </button>
                                    <div x-show="expanded" x-collapse class="mt-1">
                                        <x-attachment-list :media="$commentMedia" compact :show-delete="false" />
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center text-base-content/50 py-6">No comments yet.</div>
                @endforelse
            </div>

            {{-- Reminders tab --}}
            <div x-show="tab === 'reminders'" class="mt-4 space-y-4">
                {{-- Existing reminders --}}
                @forelse ($ticket->reminders as $reminder)
                    <div
                        x-data="{ editing: false }"
                        class="card bg-base-100 shadow"
                    >
                        {{-- View mode --}}
                        <div x-show="!editing" class="card-body py-3">
                            <div class="flex items-start justify-between gap-2">
                                <div class="space-y-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="font-medium text-sm">{{ $reminder->title }}</span>
                                        <span class="badge badge-outline badge-xs">{{ $reminder->type->label() }}</span>
                                        @if ($reminder->is_sent)
                                            <span class="badge badge-success badge-xs">Sent</span>
                                        @elseif ($reminder->remind_at->isPast())
                                            <span class="badge badge-warning badge-xs">Overdue</span>
                                        @endif
                                        @if ($reminder->is_recurring)
                                            <span class="badge badge-info badge-xs">Recurring</span>
                                        @endif
                                    </div>
                                    <div class="text-sm text-base-content/60">
                                        {{ $reminder->remind_at->format('d M Y H:i') }}
                                    </div>
                                    @if ($reminder->notes)
                                        <div class="text-sm text-base-content/70">{{ $reminder->notes }}</div>
                                    @endif
                                </div>
                                <div class="flex gap-1">
                                    <button @click="editing = true" type="button" class="btn btn-xs btn-ghost">Edit</button>
                                    <form
                                        method="POST"
                                        action="{{ route('tickets.reminders.destroy', [$ticket, $reminder]) }}"
                                        x-data
                                        @submit.prevent="if (confirm('Delete this reminder?')) $el.submit()"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-xs btn-ghost text-error">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- Edit mode --}}
                        <div x-show="editing" class="card-body py-3">
                            <form method="POST" action="{{ route('tickets.reminders.update', [$ticket, $reminder]) }}">
                                @csrf
                                @method('PUT')

                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <div class="form-control sm:col-span-2">
                                        <label class="label"><span class="label-text text-sm">Title</span></label>
                                        <input type="text" name="title" value="{{ $reminder->title }}" class="input input-bordered input-sm" required>
                                    </div>
                                    <div class="form-control">
                                        <label class="label"><span class="label-text text-sm">Remind At</span></label>
                                        <input type="datetime-local" name="remind_at" value="{{ $reminder->remind_at->format('Y-m-d\TH:i') }}" class="input input-bordered input-sm" required>
                                    </div>
                                    <div class="form-control">
                                        <label class="label"><span class="label-text text-sm">Type</span></label>
                                        <select name="type" class="select select-bordered select-sm" required>
                                            @foreach (\App\Enums\ReminderType::cases() as $type)
                                                <option value="{{ $type->value }}" {{ $reminder->type === $type ? 'selected' : '' }}>{{ $type->label() }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-control sm:col-span-2">
                                        <label class="label"><span class="label-text text-sm">Notes</span></label>
                                        <textarea name="notes" rows="2" class="textarea textarea-bordered">{{ $reminder->notes }}</textarea>
                                    </div>
                                </div>

                                <div class="flex justify-end gap-2 mt-3">
                                    <button @click="editing = false" type="button" class="btn btn-ghost btn-sm">Cancel</button>
                                    <button type="submit" class="btn btn-primary btn-sm">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-base-content/50 py-6">No reminders yet.</div>
                @endforelse

                {{-- Add reminder form --}}
                <div class="card bg-base-100 shadow">
                    <div class="card-body">
                        <h3 class="font-medium mb-3">Add Reminder</h3>
                        <form method="POST" action="{{ route('tickets.reminders.store', $ticket) }}">
                            @csrf

                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div class="form-control sm:col-span-2">
                                    <label class="label" for="reminder_title">
                                        <span class="label-text text-sm">Title <span class="text-error">*</span></span>
                                    </label>
                                    <input
                                        id="reminder_title"
                                        type="text"
                                        name="title"
                                        value="{{ old('title') }}"
                                        class="input input-bordered @error('title') input-error @enderror"
                                        required
                                    >
                                    @error('title')
                                        <span class="label-text-alt text-error mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-control">
                                    <label class="label" for="remind_at">
                                        <span class="label-text text-sm">Remind At <span class="text-error">*</span></span>
                                    </label>
                                    <input
                                        id="remind_at"
                                        type="datetime-local"
                                        name="remind_at"
                                        value="{{ old('remind_at') }}"
                                        class="input input-bordered @error('remind_at') input-error @enderror"
                                        required
                                    >
                                    @error('remind_at')
                                        <span class="label-text-alt text-error mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-control">
                                    <label class="label" for="reminder_type">
                                        <span class="label-text text-sm">Type <span class="text-error">*</span></span>
                                    </label>
                                    <select id="reminder_type" name="type" class="select select-bordered @error('type') select-error @enderror" required>
                                        @foreach (\App\Enums\ReminderType::cases() as $type)
                                            <option value="{{ $type->value }}" {{ old('type', \App\Enums\ReminderType::Custom->value) === $type->value ? 'selected' : '' }}>
                                                {{ $type->label() }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('type')
                                        <span class="label-text-alt text-error mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-control sm:col-span-2">
                                    <label class="label" for="reminder_notes">
                                        <span class="label-text text-sm">Notes</span>
                                    </label>
                                    <textarea
                                        id="reminder_notes"
                                        name="notes"
                                        rows="2"
                                        class="textarea textarea-bordered"
                                    >{{ old('notes') }}</textarea>
                                </div>
                            </div>

                            <div class="flex justify-end mt-4">
                                <button type="submit" class="btn btn-primary btn-sm">Add Reminder</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
