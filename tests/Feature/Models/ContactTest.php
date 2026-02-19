<?php

use App\Enums\ContactType;
use App\Models\Contact;
use App\Models\Ticket;
use App\Models\TicketType;
use App\Models\User;

it('can be created with a factory', function () {
    $contact = Contact::factory()->create();

    expect($contact)->toBeInstanceOf(Contact::class)
        ->and($contact->exists)->toBeTrue();
});

it('casts type to ContactType enum', function () {
    $contact = Contact::factory()->create(['type' => ContactType::Authority]);

    expect($contact->fresh()->type)->toBe(ContactType::Authority);
});

it('supports soft deletes', function () {
    $contact = Contact::factory()->create();
    $contact->delete();

    expect(Contact::find($contact->id))->toBeNull()
        ->and(Contact::withTrashed()->find($contact->id))->not->toBeNull();
});

it('has many tickets', function () {
    $user = User::factory()->create();
    $ticketType = TicketType::factory()->create();
    $contact = Contact::factory()->create();

    Ticket::factory()->count(2)->create([
        'user_id' => $user->id,
        'ticket_type_id' => $ticketType->id,
        'filed_with_contact_id' => $contact->id,
    ]);

    expect($contact->tickets)->toHaveCount(2);
});
