<?php

use App\Enums\TicketStatus;
use App\Models\Reminder;
use App\Models\Ticket;
use App\Models\User;
use App\Notifications\ReminderDueNotification;
use Illuminate\Support\Facades\Notification;

it('sends notifications for due reminders', function () {
    Notification::fake();

    $user = User::factory()->create();
    $reminder = Reminder::factory()->create([
        'user_id' => $user->id,
        'is_sent' => false,
        'remind_at' => now()->subMinutes(5),
    ]);

    $this->artisan('reminders:send')->assertSuccessful();

    Notification::assertSentTo($user, ReminderDueNotification::class);
    expect($reminder->fresh()->is_sent)->toBeTrue()
        ->and($reminder->fresh()->sent_at)->not->toBeNull();
});

it('skips reminders that are not yet due', function () {
    Notification::fake();

    $user = User::factory()->create();
    Reminder::factory()->create([
        'user_id' => $user->id,
        'is_sent' => false,
        'remind_at' => now()->addHour(),
    ]);

    $this->artisan('reminders:send')->assertSuccessful();

    Notification::assertNothingSent();
});

it('skips reminders that are already sent', function () {
    Notification::fake();

    $user = User::factory()->create();
    Reminder::factory()->create([
        'user_id' => $user->id,
        'is_sent' => true,
        'sent_at' => now()->subHour(),
        'remind_at' => now()->subHour(),
    ]);

    $this->artisan('reminders:send')->assertSuccessful();

    Notification::assertNothingSent();
});

it('skips reminders for escalated tickets with open child tickets', function () {
    Notification::fake();

    $user = User::factory()->create();
    $parentTicket = Ticket::factory()->create([
        'status' => TicketStatus::Escalated->value,
    ]);
    Ticket::factory()->create([
        'parent_ticket_id' => $parentTicket->id,
        'status' => TicketStatus::InProgress->value,
    ]);
    Reminder::factory()->create([
        'user_id' => $user->id,
        'ticket_id' => $parentTicket->id,
        'is_sent' => false,
        'remind_at' => now()->subMinutes(5),
    ]);

    $this->artisan('reminders:send')->assertSuccessful();

    Notification::assertNothingSent();
});

it('sends reminders for escalated tickets when child tickets are resolved', function () {
    Notification::fake();

    $user = User::factory()->create();
    $parentTicket = Ticket::factory()->create([
        'status' => TicketStatus::Escalated->value,
    ]);
    Ticket::factory()->create([
        'parent_ticket_id' => $parentTicket->id,
        'status' => TicketStatus::Resolved->value,
    ]);
    Reminder::factory()->create([
        'user_id' => $user->id,
        'ticket_id' => $parentTicket->id,
        'is_sent' => false,
        'remind_at' => now()->subMinutes(5),
    ]);

    $this->artisan('reminders:send')->assertSuccessful();

    Notification::assertSentTo($user, ReminderDueNotification::class);
});

it('outputs the count of sent reminders', function () {
    Notification::fake();

    $user = User::factory()->create();
    Reminder::factory()->count(3)->create([
        'user_id' => $user->id,
        'is_sent' => false,
        'remind_at' => now()->subMinutes(5),
    ]);

    $this->artisan('reminders:send')
        ->expectsOutput('Sent 3 reminder notification(s).')
        ->assertSuccessful();
});
