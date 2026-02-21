<?php

namespace App\Http\Controllers;

use App\Enums\TicketStatus;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Models\Contact;
use App\Models\Ticket;
use App\Models\TicketType;
use App\Services\TicketStateMachine;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use SourcedOpen\Tags\Models\Tag;
use Spatie\Activitylog\Models\Activity;

class TicketController extends Controller
{
    public function __construct(private readonly TicketStateMachine $stateMachine) {}

    public function index(Request $request): View
    {
        $query = Ticket::query()
            ->with(['ticketType', 'filedWithContact', 'tags'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->input('priority'));
        }

        if ($request->filled('ticket_type_id')) {
            $query->where('ticket_type_id', $request->input('ticket_type_id'));
        }

        if ($request->filled('tag')) {
            $tag = $request->input('tag');
            $query->whereHas('tags', fn ($q) => $q->where('name', $tag));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('reference_number', 'like', "%{$search}%")
                    ->orWhere('external_reference', 'like', "%{$search}%");
            });
        }

        if ($request->filled('filed_from')) {
            $query->whereDate('filed_date', '>=', $request->input('filed_from'));
        }

        if ($request->filled('filed_to')) {
            $query->whereDate('filed_date', '<=', $request->input('filed_to'));
        }

        if ($request->boolean('overdue')) {
            $closedStatuses = [\App\Enums\TicketStatus::Resolved->value, \App\Enums\TicketStatus::Closed->value];
            $query->whereNotNull('due_date')
                ->whereDate('due_date', '<', today())
                ->whereNotIn('status', $closedStatuses);
        }

        $tickets = $query->paginate(20)->withQueryString();
        $ticketTypes = TicketType::query()->where('is_active', true)->orderBy('sort_order')->get();
        $allTags = Tag::query()->orderBy('name')->get();

        return view('tickets.index', compact('tickets', 'ticketTypes', 'allTags'));
    }

    public function create(Request $request): View
    {
        $ticketTypes = TicketType::query()->where('is_active', true)->orderBy('sort_order')->get();
        $parentTicketId = $request->integer('parent_ticket_id') ?: null;

        return view('tickets.create', compact('ticketTypes', 'parentTicketId'));
    }

    public function createWithType(TicketType $ticketType, Request $request): View
    {
        $contacts = Contact::query()->orderBy('name')->get();
        $allTags = Tag::query()->orderBy('name')->get();
        $schema = $ticketType->schema_definition ?? [];
        $parentTicketId = $request->integer('parent_ticket_id') ?: null;

        return view('tickets.create-form', compact('ticketType', 'contacts', 'schema', 'allTags', 'parentTicketId'));
    }

    public function store(StoreTicketRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();

        $ticket = Ticket::query()->create($data);

        $this->syncTagsAndMedia($request, $ticket, 'documents');

        return redirect()->route('tickets.show', $ticket)->with('success', 'Ticket created successfully.');
    }

    public function show(Ticket $ticket): View
    {
        $ticket->load(['ticketType', 'filedWithContact', 'comments.user', 'comments.media', 'reminders', 'tags', 'user', 'parentTicket', 'childTickets.ticketType']);
        $allowedStatuses = $this->stateMachine->allowedTransitions($ticket->status);
        $documents = $ticket->getMedia('documents');

        $activityItems = Activity::query()
            ->forSubject($ticket)
            ->with('causer')
            ->latest()
            ->get();

        $timeline = collect($ticket->comments)
            ->map(fn ($c) => ['type' => 'comment', 'at' => $c->commented_at, 'item' => $c])
            ->merge(
                $activityItems->map(fn ($a) => ['type' => 'activity', 'at' => $a->created_at, 'item' => $a])
            )
            ->sortByDesc('at')
            ->values();

        return view('tickets.show', compact('ticket', 'allowedStatuses', 'documents', 'timeline'));
    }

    public function edit(Ticket $ticket): View
    {
        $ticket->load(['ticketType', 'tags']);
        $contacts = Contact::query()->orderBy('name')->get();
        $ticketTypes = TicketType::query()->where('is_active', true)->orderBy('sort_order')->get();
        $allTags = Tag::query()->orderBy('name')->get();
        $schema = $ticket->ticketType?->schema_definition ?? [];
        $documents = $ticket->getMedia('documents');

        return view('tickets.edit', compact('ticket', 'contacts', 'ticketTypes', 'schema', 'allTags', 'documents'));
    }

    public function update(UpdateTicketRequest $request, Ticket $ticket): RedirectResponse
    {
        $data = $request->validated();
        $newStatus = TicketStatus::from($data['status']);

        if (! $this->stateMachine->canTransition($ticket->status, $newStatus)) {
            return back()->withErrors(['status' => "Cannot transition from {$ticket->status->label()} to {$newStatus->label()}."]);
        }

        if ($newStatus === TicketStatus::Closed && $ticket->status !== TicketStatus::Closed) {
            $data['closed_date'] = now()->toDateString();
        }

        $ticket->update($data);

        $this->syncTagsAndMedia($request, $ticket, 'documents');

        return redirect()->route('tickets.show', $ticket)->with('success', 'Ticket updated successfully.');
    }

    public function destroy(Ticket $ticket): RedirectResponse
    {
        $ticket->delete();

        return redirect()->route('tickets.index')->with('success', 'Ticket deleted successfully.');
    }

    private function syncTagsAndMedia(Request $request, Ticket $ticket, string $collection): void
    {
        $tagNames = array_filter(
            array_map('trim', explode(',', $request->input('tags', '')))
        );
        $tagIds = collect($tagNames)
            ->map(fn ($name) => Tag::firstOrCreate(['name' => $name])->id)
            ->toArray();
        $ticket->syncTags($tagIds);

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $ticket->addMedia($file)->toMediaCollection($collection);
            }
        }
    }
}
