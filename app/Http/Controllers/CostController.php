<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCostRequest;
use App\Http\Requests\UpdateCostRequest;
use App\Models\Cost;
use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;

class CostController extends Controller
{
    public function store(StoreCostRequest $request, Ticket $ticket): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();

        $ticket->costs()->create($data);

        return redirect()->route('tickets.show', $ticket)->with('success', 'Cost added.');
    }

    public function update(UpdateCostRequest $request, Ticket $ticket, Cost $cost): RedirectResponse
    {
        abort_unless($cost->ticket_id === $ticket->id, 404);

        $cost->update($request->validated());

        return redirect()->route('tickets.show', $ticket)->with('success', 'Cost updated.');
    }

    public function destroy(Ticket $ticket, Cost $cost): RedirectResponse
    {
        abort_unless($cost->ticket_id === $ticket->id, 404);

        $cost->delete();

        return redirect()->route('tickets.show', $ticket)->with('success', 'Cost deleted.');
    }
}
