<?php

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\User;
use App\Services\TicketStateMachine;

it('allows valid status transition via HTTP', function () {
    $user = User::factory()->create();
    $ticket = Ticket::factory()->create(['user_id' => $user->id, 'status' => TicketStatus::Draft]);

    $this->actingAs($user)
        ->put(route('tickets.update', $ticket), [
            'title' => $ticket->title,
            'ticket_type_id' => $ticket->ticket_type_id,
            'status' => TicketStatus::Submitted->value,
            'priority' => $ticket->priority->value,
        ])
        ->assertRedirect(route('tickets.show', $ticket));

    expect($ticket->fresh()->status)->toBe(TicketStatus::Submitted);
});

it('rejects invalid status transition via HTTP', function () {
    $user = User::factory()->create();
    $ticket = Ticket::factory()->create(['user_id' => $user->id, 'status' => TicketStatus::Draft]);

    $this->actingAs($user)
        ->put(route('tickets.update', $ticket), [
            'title' => $ticket->title,
            'ticket_type_id' => $ticket->ticket_type_id,
            'status' => TicketStatus::Resolved->value,
            'priority' => $ticket->priority->value,
        ])
        ->assertSessionHasErrors('status');

    expect($ticket->fresh()->status)->toBe(TicketStatus::Draft);
});

it('state machine allows same status as no-op', function () {
    $machine = new TicketStateMachine;

    expect($machine->canTransition(TicketStatus::Draft, TicketStatus::Draft))->toBeTrue();
});

it('state machine validates all defined transitions', function (TicketStatus $from, TicketStatus $to) {
    $machine = new TicketStateMachine;

    expect($machine->canTransition($from, $to))->toBeTrue();
})->with([
    'draft → submitted' => [TicketStatus::Draft, TicketStatus::Submitted],
    'submitted → acknowledged' => [TicketStatus::Submitted, TicketStatus::Acknowledged],
    'submitted → in_progress' => [TicketStatus::Submitted, TicketStatus::InProgress],
    'submitted → closed' => [TicketStatus::Submitted, TicketStatus::Closed],
    'acknowledged → in_progress' => [TicketStatus::Acknowledged, TicketStatus::InProgress],
    'acknowledged → closed' => [TicketStatus::Acknowledged, TicketStatus::Closed],
    'in_progress → resolved' => [TicketStatus::InProgress, TicketStatus::Resolved],
    'in_progress → escalated' => [TicketStatus::InProgress, TicketStatus::Escalated],
    'in_progress → closed' => [TicketStatus::InProgress, TicketStatus::Closed],
    'escalated → resolved' => [TicketStatus::Escalated, TicketStatus::Resolved],
    'escalated → closed' => [TicketStatus::Escalated, TicketStatus::Closed],
    'resolved → closed' => [TicketStatus::Resolved, TicketStatus::Closed],
    'closed → reopened' => [TicketStatus::Closed, TicketStatus::Reopened],
    'reopened → in_progress' => [TicketStatus::Reopened, TicketStatus::InProgress],
    'reopened → closed' => [TicketStatus::Reopened, TicketStatus::Closed],
]);

it('state machine rejects invalid transitions', function (TicketStatus $from, TicketStatus $to) {
    $machine = new TicketStateMachine;

    expect($machine->canTransition($from, $to))->toBeFalse();
})->with([
    'draft → resolved' => [TicketStatus::Draft, TicketStatus::Resolved],
    'draft → closed' => [TicketStatus::Draft, TicketStatus::Closed],
    'resolved → draft' => [TicketStatus::Resolved, TicketStatus::Draft],
    'closed → draft' => [TicketStatus::Closed, TicketStatus::Draft],
    'submitted → reopened' => [TicketStatus::Submitted, TicketStatus::Reopened],
]);
