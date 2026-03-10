<?php

namespace App\Http\Controllers;

use App\Enums\TicketStatus;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Models\Contact;
use App\Models\Ticket;
use App\Models\TicketType;
use App\QueryBuilders\Sorts\NullsLastSort;
use App\Services\TicketStateMachine;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use SourcedOpen\Tags\Models\Tag;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class TicketController extends Controller
{
    public function __construct(private readonly TicketStateMachine $stateMachine) {}

    public function index(): View
    {
        $tickets = QueryBuilder::for(Ticket::class)
            ->with(['ticketType', 'filedWithContact', 'tags'])
            ->allowedFilters([
                AllowedFilter::exact('status'),
                AllowedFilter::exact('priority'),
                AllowedFilter::exact('ticket_type_id'),
                AllowedFilter::callback('tag', fn (Builder $query, $value) => $query->whereHas('tags', fn (Builder $q) => $q->where('name', $value))),
                AllowedFilter::callback('search', function (Builder $query, $value): void {
                    $query->where(function (Builder $q) use ($value) {
                        $q->where('title', 'like', "%{$value}%")
                            ->orWhere('description', 'like', "%{$value}%")
                            ->orWhere('reference_number', 'like', "%{$value}%")
                            ->orWhere('external_reference', 'like', "%{$value}%");
                    });
                }),
                AllowedFilter::callback('filed_from', fn (Builder $query, $value) => $query->whereDate('filed_date', '>=', $value)),
                AllowedFilter::callback('filed_to', fn (Builder $query, $value) => $query->whereDate('filed_date', '<=', $value)),
                AllowedFilter::callback('overdue', function (Builder $query, $value): void {
                    if ($value) {
                        $completedValues = array_map(fn (TicketStatus $s) => $s->value, TicketStatus::completedStatuses());
                        $query->whereNotNull('due_date')
                            ->whereDate('due_date', '<', today())
                            ->whereNotIn('status', $completedValues);
                    }
                }),
                AllowedFilter::callback('open', function (Builder $query, $value): void {
                    if (request()->filled('filter.status')) {
                        return;
                    }

                    if ($value) {
                        $completedValues = array_map(fn (TicketStatus $s) => $s->value, TicketStatus::completedStatuses());
                        $query->whereNotIn('status', $completedValues);
                    }
                })->default(true),
            ])
            ->defaultSort(AllowedSort::custom('due_date', new NullsLastSort))
            ->allowedSorts([
                AllowedSort::custom('due_date', new NullsLastSort),
            ])
            ->paginate(20)
            ->withQueryString();

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

        $ticket = DB::transaction(function () use ($data, $request) {
            $ticket = Ticket::query()->create($data);
            $this->syncTagsAndMedia($request, $ticket, 'documents');

            if ($request->filled('cost_amount')) {
                $ticket->costs()->create([
                    'amount' => $data['cost_amount'],
                    'description' => $data['cost_description'] ?? null,
                    'incurred_at' => $data['cost_incurred_at'],
                    'user_id' => auth()->id(),
                ]);
            }

            return $ticket;
        });

        return redirect()->route('tickets.show', $ticket)->with('success', 'Ticket created successfully.');
    }

    public function show(Ticket $ticket): View
    {
        $ticket->load(['ticketType', 'filedWithContact', 'comments.user', 'comments.media', 'reminders', 'costs.user', 'tags', 'user', 'parentTicket', 'childTickets.ticketType']);
        $allowedStatuses = array_filter(
            $this->stateMachine->allowedTransitions($ticket->status),
            fn (TicketStatus $status) => $status !== TicketStatus::Escalated || $ticket->childTickets->isNotEmpty(),
        );
        $documents = $ticket->getMedia('documents');
        $comments = $ticket->comments->sortByDesc('commented_at')->values();
        $totalCost = $ticket->costs->sum('amount');

        return view('tickets.show', compact('ticket', 'allowedStatuses', 'documents', 'comments', 'totalCost'));
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

        if ($newStatus === TicketStatus::Escalated && ! $ticket->childTickets()->exists()) {
            return back()->withErrors(['status' => 'A ticket can only be escalated if it has at least one follow-up ticket.']);
        }

        if ($newStatus === TicketStatus::Closed && $ticket->status !== TicketStatus::Closed) {
            $data['closed_date'] = now()->toDateString();
        }

        DB::transaction(function () use ($data, $request, $ticket) {
            $ticket->update($data);
            $this->syncTagsAndMedia($request, $ticket, 'documents');
        });

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
