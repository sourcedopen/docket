<?php

use App\Enums\ReminderType;
use App\Models\Reminder;
use App\Models\Ticket;
use App\Models\User;

it('can be created with a factory', function () {
    $reminder = Reminder::factory()->create();

    expect($reminder)->toBeInstanceOf(Reminder::class)
        ->and($reminder->exists)->toBeTrue();
});

it('casts type to ReminderType enum', function () {
    $reminder = Reminder::factory()->create(['type' => ReminderType::DeadlineApproaching]);

    expect($reminder->fresh()->type)->toBe(ReminderType::DeadlineApproaching);
});

it('casts remind_at as datetime', function () {
    $reminder = Reminder::factory()->create(['remind_at' => '2026-03-01 09:00:00']);

    expect($reminder->fresh()->remind_at)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
});

it('casts boolean fields', function () {
    $reminder = Reminder::factory()->create(['is_sent' => true, 'is_recurring' => true]);
    $fresh = $reminder->fresh();

    expect($fresh->is_sent)->toBeTrue()
        ->and($fresh->is_recurring)->toBeTrue();
});

it('supports soft deletes', function () {
    $reminder = Reminder::factory()->create();
    $reminder->delete();

    expect(Reminder::find($reminder->id))->toBeNull()
        ->and(Reminder::withTrashed()->find($reminder->id))->not->toBeNull();
});

it('belongs to a ticket', function () {
    $ticket = Ticket::factory()->create();
    $reminder = Reminder::factory()->create(['ticket_id' => $ticket->id]);

    expect($reminder->ticket->id)->toBe($ticket->id);
});

it('belongs to a user', function () {
    $user = User::factory()->create();
    $reminder = Reminder::factory()->create(['user_id' => $user->id]);

    expect($reminder->user->id)->toBe($user->id);
});
