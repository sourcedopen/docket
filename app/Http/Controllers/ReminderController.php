<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReminderRequest;
use App\Models\Reminder;
use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;

class ReminderController extends Controller
{
    public function store(StoreReminderRequest $request, Ticket $ticket): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $data['is_recurring'] = $request->boolean('is_recurring');

        $ticket->reminders()->create($data);

        return redirect()->route('tickets.show', $ticket)->withFragment('reminders')->with('success', 'Reminder added.');
    }

    public function update(StoreReminderRequest $request, Ticket $ticket, Reminder $reminder): RedirectResponse
    {
        abort_unless($reminder->ticket_id === $ticket->id, 404);

        $data = $request->validated();
        $data['is_recurring'] = $request->boolean('is_recurring');

        $reminder->update($data);

        return redirect()->route('tickets.show', $ticket)->withFragment('reminders')->with('success', 'Reminder updated.');
    }

    public function destroy(Ticket $ticket, Reminder $reminder): RedirectResponse
    {
        abort_unless($reminder->ticket_id === $ticket->id, 404);

        $reminder->delete();

        return redirect()->route('tickets.show', $ticket)->withFragment('reminders')->with('success', 'Reminder deleted.');
    }
}
