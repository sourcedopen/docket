<?php

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\User;

it('sorts tickets by due date ascending with nulls last by default', function () {
    $user = User::factory()->create();

    $ticketNoDue = Ticket::factory()->create(['due_date' => null]);
    $ticketLateDue = Ticket::factory()->create(['due_date' => now()->addDays(10)]);
    $ticketEarlyDue = Ticket::factory()->create(['due_date' => now()->addDays(2)]);

    $response = $this->actingAs($user)->get(route('tickets.index', ['filter' => ['open' => '1']]));

    $response->assertOk();

    $tickets = $response->viewData('tickets');
    $ids = $tickets->pluck('id')->all();

    expect($ids[0])->toBe($ticketEarlyDue->id)
        ->and($ids[1])->toBe($ticketLateDue->id)
        ->and($ids[2])->toBe($ticketNoDue->id);
});

it('shows only open tickets by default', function () {
    $user = User::factory()->create();

    $openTicket = Ticket::factory()->create(['status' => TicketStatus::InProgress]);
    $closedTicket = Ticket::factory()->create(['status' => TicketStatus::Closed]);
    $resolvedTicket = Ticket::factory()->create(['status' => TicketStatus::Resolved]);

    $response = $this->actingAs($user)->get(route('tickets.index', ['filter' => ['open' => '1']]));

    $tickets = $response->viewData('tickets');
    $ids = $tickets->pluck('id')->all();

    expect($ids)->toContain($openTicket->id)
        ->not->toContain($closedTicket->id)
        ->not->toContain($resolvedTicket->id);
});

it('shows all tickets when open filter is unchecked', function () {
    $user = User::factory()->create();

    $openTicket = Ticket::factory()->create(['status' => TicketStatus::InProgress]);
    $closedTicket = Ticket::factory()->create(['status' => TicketStatus::Closed]);
    $resolvedTicket = Ticket::factory()->create(['status' => TicketStatus::Resolved]);

    $response = $this->actingAs($user)->get(route('tickets.index', ['filter' => ['open' => '0']]));

    $tickets = $response->viewData('tickets');
    $ids = $tickets->pluck('id')->all();

    expect($ids)->toContain($openTicket->id)
        ->and($ids)->toContain($closedTicket->id)
        ->and($ids)->toContain($resolvedTicket->id);
});

it('ignores open filter when a specific status is selected', function () {
    $user = User::factory()->create();

    $closedTicket = Ticket::factory()->create(['status' => TicketStatus::Closed]);

    $response = $this->actingAs($user)->get(route('tickets.index', ['filter' => ['status' => TicketStatus::Closed->value]]));

    $tickets = $response->viewData('tickets');
    $ids = $tickets->pluck('id')->all();

    expect($ids)->toContain($closedTicket->id);
});
