<?php

use App\Models\Cost;
use App\Models\Ticket;
use App\Models\User;

it('stores a cost', function () {
    $user = User::factory()->create();
    $ticket = Ticket::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->post(route('tickets.costs.store', $ticket), [
            'amount' => 150.50,
            'description' => 'Filing fee',
            'incurred_at' => '2026-02-28',
        ])
        ->assertRedirect(route('tickets.show', $ticket));

    $cost = $ticket->costs()->first();

    expect($cost)->not->toBeNull()
        ->and($cost->amount)->toBe('150.50')
        ->and($cost->description)->toBe('Filing fee')
        ->and($cost->user_id)->toBe($user->id);
});

it('requires amount when storing a cost', function () {
    $user = User::factory()->create();
    $ticket = Ticket::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->post(route('tickets.costs.store', $ticket), [
            'incurred_at' => '2026-02-28',
        ])
        ->assertSessionHasErrors('amount');
});

it('requires incurred_at when storing a cost', function () {
    $user = User::factory()->create();
    $ticket = Ticket::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->post(route('tickets.costs.store', $ticket), [
            'amount' => 100,
        ])
        ->assertSessionHasErrors('incurred_at');
});

it('rejects non-numeric amount', function () {
    $user = User::factory()->create();
    $ticket = Ticket::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->post(route('tickets.costs.store', $ticket), [
            'amount' => 'not-a-number',
            'incurred_at' => '2026-02-28',
        ])
        ->assertSessionHasErrors('amount');
});

it('allows nullable description', function () {
    $user = User::factory()->create();
    $ticket = Ticket::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->post(route('tickets.costs.store', $ticket), [
            'amount' => 50,
            'incurred_at' => '2026-02-28',
        ])
        ->assertRedirect(route('tickets.show', $ticket));

    expect($ticket->costs()->first()->description)->toBeNull();
});

it('updates a cost', function () {
    $user = User::factory()->create();
    $ticket = Ticket::factory()->create(['user_id' => $user->id]);
    $cost = Cost::factory()->create(['ticket_id' => $ticket->id, 'user_id' => $user->id]);

    $this->actingAs($user)
        ->put(route('tickets.costs.update', [$ticket, $cost]), [
            'amount' => 200.75,
            'description' => 'Updated fee',
            'incurred_at' => '2026-03-01',
        ])
        ->assertRedirect(route('tickets.show', $ticket));

    $cost->refresh();

    expect($cost->amount)->toBe('200.75')
        ->and($cost->description)->toBe('Updated fee');
});

it('deletes a cost', function () {
    $user = User::factory()->create();
    $ticket = Ticket::factory()->create(['user_id' => $user->id]);
    $cost = Cost::factory()->create(['ticket_id' => $ticket->id, 'user_id' => $user->id]);

    $this->actingAs($user)
        ->delete(route('tickets.costs.destroy', [$ticket, $cost]))
        ->assertRedirect(route('tickets.show', $ticket));

    expect(Cost::find($cost->id))->toBeNull();
});

it('returns 404 when cost does not belong to ticket', function () {
    $user = User::factory()->create();
    $ticket = Ticket::factory()->create(['user_id' => $user->id]);
    $otherTicket = Ticket::factory()->create(['user_id' => $user->id]);
    $cost = Cost::factory()->create(['ticket_id' => $otherTicket->id, 'user_id' => $user->id]);

    $this->actingAs($user)
        ->put(route('tickets.costs.update', [$ticket, $cost]), [
            'amount' => 100,
            'incurred_at' => '2026-02-28',
        ])
        ->assertNotFound();

    $this->actingAs($user)
        ->delete(route('tickets.costs.destroy', [$ticket, $cost]))
        ->assertNotFound();
});
