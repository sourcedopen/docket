<?php

use App\Models\Ticket;
use App\Models\User;

it('sorts tickets by due date ascending with nulls last by default', function () {
    $user = User::factory()->create();

    $ticketNoDue = Ticket::factory()->create(['due_date' => null]);
    $ticketLateDue = Ticket::factory()->create(['due_date' => now()->addDays(10)]);
    $ticketEarlyDue = Ticket::factory()->create(['due_date' => now()->addDays(2)]);

    $response = $this->actingAs($user)->get(route('tickets.index'));

    $response->assertOk();

    $tickets = $response->viewData('tickets');
    $ids = $tickets->pluck('id')->all();

    expect($ids[0])->toBe($ticketEarlyDue->id)
        ->and($ids[1])->toBe($ticketLateDue->id)
        ->and($ids[2])->toBe($ticketNoDue->id);
});
