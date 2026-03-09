<?php

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\User;
use App\Notifications\TicketOverdueNotification;
use Illuminate\Support\Facades\Notification;

it('sends overdue notifications for active tickets', function () {
    Notification::fake();

    $user = User::factory()->create();
    Ticket::factory()->create([
        'user_id' => $user->id,
        'status' => TicketStatus::InProgress,
        'due_date' => today()->subDay(),
    ]);

    $this->artisan('tickets:check-overdue')->assertSuccessful();

    Notification::assertSentTo($user, TicketOverdueNotification::class);
});

it('skips resolved tickets', function () {
    Notification::fake();

    $user = User::factory()->create();
    Ticket::factory()->create([
        'user_id' => $user->id,
        'status' => TicketStatus::Resolved,
        'due_date' => today()->subDay(),
    ]);

    $this->artisan('tickets:check-overdue')->assertSuccessful();

    Notification::assertNothingSent();
});

it('skips closed tickets', function () {
    Notification::fake();

    $user = User::factory()->create();
    Ticket::factory()->create([
        'user_id' => $user->id,
        'status' => TicketStatus::Closed,
        'due_date' => today()->subDay(),
    ]);

    $this->artisan('tickets:check-overdue')->assertSuccessful();

    Notification::assertNothingSent();
});

it('skips escalated tickets', function () {
    Notification::fake();

    $user = User::factory()->create();
    Ticket::factory()->create([
        'user_id' => $user->id,
        'status' => TicketStatus::Escalated,
        'due_date' => today()->subDay(),
    ]);

    $this->artisan('tickets:check-overdue')->assertSuccessful();

    Notification::assertNothingSent();
});

it('outputs the count of sent notifications', function () {
    Notification::fake();

    $user = User::factory()->create();
    Ticket::factory()->count(2)->create([
        'user_id' => $user->id,
        'status' => TicketStatus::InProgress,
        'due_date' => today()->subDay(),
    ]);

    $this->artisan('tickets:check-overdue')
        ->expectsOutput('Sent 2 overdue ticket notification(s).')
        ->assertSuccessful();
});
