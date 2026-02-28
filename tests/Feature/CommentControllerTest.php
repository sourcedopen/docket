<?php

use App\Enums\CommentType;
use App\Models\Comment;
use App\Models\Ticket;
use App\Models\User;

it('stores a comment with commented_at', function () {
    $user = User::factory()->create();
    $ticket = Ticket::factory()->create(['user_id' => $user->id]);
    $commentedAt = now()->subDays(3)->startOfMinute();

    $this->actingAs($user)
        ->post(route('tickets.comments.store', $ticket), [
            'body' => 'A backdated update.',
            'type' => CommentType::Update->value,
            'commented_at' => $commentedAt->format('Y-m-d\TH:i'),
        ])
        ->assertRedirect(route('tickets.show', $ticket).'#timeline');

    $comment = $ticket->comments()->first();

    expect($comment)->not->toBeNull()
        ->and($comment->body)->toBe('A backdated update.')
        ->and($comment->commented_at->format('Y-m-d H:i'))->toBe($commentedAt->format('Y-m-d H:i'));
});

it('defaults commented_at to current time when not provided via factory', function () {
    $before = now()->startOfMinute();
    $comment = Comment::factory()->create();
    $after = now()->addMinute();

    expect($comment->commented_at)->toBeGreaterThanOrEqual($before)
        ->and($comment->commented_at)->toBeLessThanOrEqual($after);
});

it('stores a comment with backdated commented_at', function () {
    $user = User::factory()->create();
    $ticket = Ticket::factory()->create(['user_id' => $user->id]);
    $pastDate = '2024-06-15T09:30';

    $this->actingAs($user)
        ->post(route('tickets.comments.store', $ticket), [
            'body' => 'A response received last year.',
            'type' => CommentType::ResponseReceived->value,
            'commented_at' => $pastDate,
        ])
        ->assertRedirect();

    $comment = $ticket->comments()->first();

    expect($comment->commented_at->format('Y-m-d H:i'))->toBe('2024-06-15 09:30');
});

it('requires commented_at when storing a comment', function () {
    $user = User::factory()->create();
    $ticket = Ticket::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->post(route('tickets.comments.store', $ticket), [
            'body' => 'Missing date.',
            'type' => CommentType::Update->value,
        ])
        ->assertSessionHasErrors('commented_at');
});

it('deletes a comment', function () {
    $user = User::factory()->create();
    $ticket = Ticket::factory()->create(['user_id' => $user->id]);
    $comment = Comment::factory()->create(['ticket_id' => $ticket->id, 'user_id' => $user->id]);

    $this->actingAs($user)
        ->delete(route('tickets.comments.destroy', [$ticket, $comment]))
        ->assertRedirect(route('tickets.show', $ticket).'#timeline');

    expect(Comment::find($comment->id))->toBeNull();
});
