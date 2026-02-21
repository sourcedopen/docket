<?php

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Comment;
use App\Models\Contact;
use App\Models\Reminder;
use App\Models\Ticket;
use App\Models\TicketType;
use App\Models\User;

it('can be created with a factory', function () {
    $ticket = Ticket::factory()->create();

    expect($ticket)->toBeInstanceOf(Ticket::class)
        ->and($ticket->exists)->toBeTrue();
});

it('casts status to TicketStatus enum', function () {
    $ticket = Ticket::factory()->create(['status' => TicketStatus::Submitted]);

    expect($ticket->fresh()->status)->toBe(TicketStatus::Submitted);
});

it('casts priority to TicketPriority enum', function () {
    $ticket = Ticket::factory()->create(['priority' => TicketPriority::High]);

    expect($ticket->fresh()->priority)->toBe(TicketPriority::High);
});

it('casts custom_fields as array', function () {
    $fields = ['pio_name' => 'John Doe', 'fee_paid' => 10];
    $ticket = Ticket::factory()->create(['custom_fields' => $fields]);

    expect($ticket->fresh()->custom_fields)->toBe($fields);
});

it('casts date fields', function () {
    $ticket = Ticket::factory()->create([
        'filed_date' => '2026-01-15',
        'due_date' => '2026-02-14',
    ]);

    expect($ticket->fresh()->filed_date)->toBeInstanceOf(\Carbon\CarbonImmutable::class)
        ->and($ticket->fresh()->due_date)->toBeInstanceOf(\Carbon\CarbonImmutable::class);
});

it('supports soft deletes', function () {
    $ticket = Ticket::factory()->create();
    $ticket->delete();

    expect(Ticket::find($ticket->id))->toBeNull()
        ->and(Ticket::withTrashed()->find($ticket->id))->not->toBeNull();
});

it('belongs to a user', function () {
    $user = User::factory()->create();
    $ticket = Ticket::factory()->create(['user_id' => $user->id]);

    expect($ticket->user->id)->toBe($user->id);
});

it('belongs to a ticket type', function () {
    $ticketType = TicketType::factory()->create();
    $ticket = Ticket::factory()->create(['ticket_type_id' => $ticketType->id]);

    expect($ticket->ticketType->id)->toBe($ticketType->id);
});

it('belongs to a contact when filed_with_contact_id is set', function () {
    $contact = Contact::factory()->create();
    $ticket = Ticket::factory()->create(['filed_with_contact_id' => $contact->id]);

    expect($ticket->filedWithContact->id)->toBe($contact->id);
});

it('has many comments', function () {
    $ticket = Ticket::factory()->create();
    Comment::factory()->count(3)->create(['ticket_id' => $ticket->id]);

    expect($ticket->comments)->toHaveCount(3);
});

it('has many reminders', function () {
    $ticket = Ticket::factory()->create(['due_date' => null]);
    Reminder::factory()->count(2)->create(['ticket_id' => $ticket->id]);

    expect($ticket->reminders)->toHaveCount(2);
});

it('can have a parent ticket', function () {
    $parent = Ticket::factory()->create();
    $child = Ticket::factory()->create(['parent_ticket_id' => $parent->id]);

    expect($child->parentTicket->id)->toBe($parent->id);
});

it('can have child tickets', function () {
    $parent = Ticket::factory()->create();
    Ticket::factory()->count(2)->create(['parent_ticket_id' => $parent->id]);

    expect($parent->childTickets)->toHaveCount(2);
});
