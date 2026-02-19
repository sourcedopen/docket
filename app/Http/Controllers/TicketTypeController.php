<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketTypeRequest;
use App\Http\Requests\UpdateTicketTypeRequest;
use App\Models\TicketType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TicketTypeController extends Controller
{
    public function index(): View
    {
        $ticketTypes = TicketType::query()->orderBy('sort_order')->paginate(20);

        return view('ticket-types.index', compact('ticketTypes'));
    }

    public function create(): View
    {
        return view('ticket-types.create');
    }

    public function store(StoreTicketTypeRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        if (isset($data['schema_definition']) && is_string($data['schema_definition'])) {
            $data['schema_definition'] = json_decode($data['schema_definition'], true);
        }

        $data['is_active'] = $request->boolean('is_active');

        TicketType::query()->create($data);

        return redirect()->route('ticket-types.index')->with('success', 'Ticket type created successfully.');
    }

    public function edit(TicketType $ticketType): View
    {
        return view('ticket-types.edit', compact('ticketType'));
    }

    public function update(UpdateTicketTypeRequest $request, TicketType $ticketType): RedirectResponse
    {
        $data = $request->validated();

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        if (isset($data['schema_definition']) && is_string($data['schema_definition'])) {
            $data['schema_definition'] = json_decode($data['schema_definition'], true);
        }

        $data['is_active'] = $request->boolean('is_active');

        $ticketType->update($data);

        return redirect()->route('ticket-types.index')->with('success', 'Ticket type updated successfully.');
    }

    public function destroy(TicketType $ticketType): RedirectResponse
    {
        $ticketType->delete();

        return redirect()->route('ticket-types.index')->with('success', 'Ticket type deleted successfully.');
    }

    public function schema(TicketType $ticketType): JsonResponse
    {
        return response()->json($ticketType->schema_definition);
    }
}
