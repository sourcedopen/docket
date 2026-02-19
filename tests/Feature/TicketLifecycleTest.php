<?php

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\TicketType;
use App\Models\User;

it('creates a ticket with a generated reference number', function () {
    $user = User::factory()->create();
    $ticketType = TicketType::factory()->create();

    $this->actingAs($user)
        ->post(route('tickets.store'), [
            'title' => 'Test ticket',
            'ticket_type_id' => $ticketType->id,
            'status' => TicketStatus::Draft->value,
            'priority' => TicketPriority::Medium->value,
        ])
        ->assertRedirect();

    $ticket = Ticket::query()->where('title', 'Test ticket')->firstOrFail();

    expect($ticket->reference_number)->toMatch('/^TKT-\d{4}-\d{4}$/')
        ->and($ticket->user_id)->toBe($user->id);
});

it('auto-sets due_date from ticket type SLA days', function () {
    $user = User::factory()->create();
    $ticketType = TicketType::factory()->create(['default_sla_days' => 14]);

    $this->actingAs($user)
        ->post(route('tickets.store'), [
            'title' => 'SLA test ticket',
            'ticket_type_id' => $ticketType->id,
            'status' => TicketStatus::Draft->value,
            'priority' => TicketPriority::Medium->value,
            'filed_date' => '2026-02-01',
        ]);

    $ticket = Ticket::query()->where('title', 'SLA test ticket')->firstOrFail();

    expect($ticket->due_date->toDateString())->toBe('2026-02-15');
});

it('does not overwrite an explicit due_date with SLA', function () {
    $user = User::factory()->create();
    $ticketType = TicketType::factory()->create(['default_sla_days' => 14]);

    $this->actingAs($user)
        ->post(route('tickets.store'), [
            'title' => 'Explicit due date ticket',
            'ticket_type_id' => $ticketType->id,
            'status' => TicketStatus::Draft->value,
            'priority' => TicketPriority::Medium->value,
            'filed_date' => '2026-02-01',
            'due_date' => '2026-03-01',
        ]);

    $ticket = Ticket::query()->where('title', 'Explicit due date ticket')->firstOrFail();

    expect($ticket->due_date->toDateString())->toBe('2026-03-01');
});

it('shows the ticket detail page', function () {
    $user = User::factory()->create();
    $ticket = Ticket::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->get(route('tickets.show', $ticket))
        ->assertSuccessful()
        ->assertSee($ticket->reference_number);
});

it('updates a ticket', function () {
    $user = User::factory()->create();
    $ticket = Ticket::factory()->create(['user_id' => $user->id, 'status' => TicketStatus::Draft]);

    $this->actingAs($user)
        ->put(route('tickets.update', $ticket), [
            'title' => 'Updated title',
            'ticket_type_id' => $ticket->ticket_type_id,
            'status' => TicketStatus::Draft->value,
            'priority' => TicketPriority::High->value,
        ])
        ->assertRedirect(route('tickets.show', $ticket));

    expect($ticket->fresh()->title)->toBe('Updated title')
        ->and($ticket->fresh()->priority)->toBe(TicketPriority::High);
});

it('sets closed_date when ticket is closed', function () {
    $user = User::factory()->create();
    $ticket = Ticket::factory()->create(['user_id' => $user->id, 'status' => TicketStatus::Submitted]);

    $this->actingAs($user)
        ->put(route('tickets.update', $ticket), [
            'title' => $ticket->title,
            'ticket_type_id' => $ticket->ticket_type_id,
            'status' => TicketStatus::Closed->value,
            'priority' => $ticket->priority->value,
        ]);

    expect($ticket->fresh()->closed_date)->not->toBeNull();
});

it('soft deletes a ticket', function () {
    $user = User::factory()->create();
    $ticket = Ticket::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->delete(route('tickets.destroy', $ticket))
        ->assertRedirect(route('tickets.index'));

    expect(Ticket::find($ticket->id))->toBeNull()
        ->and(Ticket::withTrashed()->find($ticket->id))->not->toBeNull();
});

it('creates a follow-up ticket linked to a parent', function () {
    $user = User::factory()->create();
    $parent = Ticket::factory()->create(['user_id' => $user->id]);
    $ticketType = TicketType::factory()->create();

    $this->actingAs($user)
        ->post(route('tickets.store'), [
            'title' => 'Follow-up ticket',
            'ticket_type_id' => $ticketType->id,
            'status' => TicketStatus::Draft->value,
            'priority' => TicketPriority::Medium->value,
            'parent_ticket_id' => $parent->id,
        ]);

    $child = Ticket::query()->where('title', 'Follow-up ticket')->firstOrFail();

    expect($child->parent_ticket_id)->toBe($parent->id)
        ->and($parent->childTickets()->count())->toBe(1);
});
