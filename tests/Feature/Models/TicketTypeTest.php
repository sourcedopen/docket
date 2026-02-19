<?php

use App\Models\Ticket;
use App\Models\TicketType;
use App\Models\User;

it('can be created with a factory', function () {
    $ticketType = TicketType::factory()->create();

    expect($ticketType)->toBeInstanceOf(TicketType::class)
        ->and($ticketType->exists)->toBeTrue();
});

it('casts schema_definition as array', function () {
    $schema = ['fields' => [['key' => 'name', 'label' => 'Name', 'type' => 'string']]];
    $ticketType = TicketType::factory()->create(['schema_definition' => $schema]);

    expect($ticketType->fresh()->schema_definition)->toBe($schema);
});

it('casts allowed_statuses as array', function () {
    $statuses = ['draft', 'submitted', 'closed'];
    $ticketType = TicketType::factory()->create(['allowed_statuses' => $statuses]);

    expect($ticketType->fresh()->allowed_statuses)->toBe($statuses);
});

it('casts is_active as boolean', function () {
    $ticketType = TicketType::factory()->create(['is_active' => true]);

    expect($ticketType->fresh()->is_active)->toBeTrue();
});

it('supports soft deletes', function () {
    $ticketType = TicketType::factory()->create();
    $ticketType->delete();

    expect(TicketType::find($ticketType->id))->toBeNull()
        ->and(TicketType::withTrashed()->find($ticketType->id))->not->toBeNull();
});

it('has many tickets', function () {
    $user = User::factory()->create();
    $ticketType = TicketType::factory()->create();

    Ticket::factory()->count(3)->create([
        'user_id' => $user->id,
        'ticket_type_id' => $ticketType->id,
    ]);

    expect($ticketType->tickets)->toHaveCount(3);
});
