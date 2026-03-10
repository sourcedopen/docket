<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Models\Contact;
use App\Models\Ticket;
use App\Models\TicketType;
use App\Services\TicketService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use SourcedOpen\Tags\Models\Tag;

class TicketController extends Controller
{
    public function __construct(private readonly TicketService $ticketService) {}

    public function index(): View
    {
        $tickets = $this->ticketService->getFilteredTickets();
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

        $ticket = $this->ticketService->createTicket(
            data: $data,
            tagNames: $this->parseTagNames($request),
            files: $request->file('files', []),
        );

        return redirect()->route('tickets.show', $ticket)->with('success', 'Ticket created successfully.');
    }

    public function show(Ticket $ticket): View
    {
        $ticket->load(['ticketType', 'filedWithContact', 'comments.user', 'comments.media', 'reminders', 'costs.user', 'tags', 'user', 'parentTicket', 'childTickets.ticketType']);
        $allowedStatuses = $this->ticketService->getAllowedStatuses($ticket);
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
        try {
            $this->ticketService->updateTicket(
                ticket: $ticket,
                data: $request->validated(),
                tagNames: $this->parseTagNames($request),
                files: $request->file('files', []),
            );
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['status' => $e->getMessage()]);
        }

        return redirect()->route('tickets.show', $ticket)->with('success', 'Ticket updated successfully.');
    }

    public function destroy(Ticket $ticket): RedirectResponse
    {
        $ticket->delete();

        return redirect()->route('tickets.index')->with('success', 'Ticket deleted successfully.');
    }

    /** @return list<string> */
    private function parseTagNames(Request $request): array
    {
        return array_filter(
            array_map('trim', explode(',', $request->input('tags', '')))
        );
    }
}
