<?php

use App\Enums\CommentType;
use App\Models\Comment;
use App\Models\Ticket;
use App\Models\User;

it('can be created with a factory', function () {
    $comment = Comment::factory()->create();

    expect($comment)->toBeInstanceOf(Comment::class)
        ->and($comment->exists)->toBeTrue();
});

it('casts type to CommentType enum', function () {
    $comment = Comment::factory()->create(['type' => CommentType::ResponseReceived]);

    expect($comment->fresh()->type)->toBe(CommentType::ResponseReceived);
});

it('casts is_internal as boolean', function () {
    $comment = Comment::factory()->create(['is_internal' => true]);

    expect($comment->fresh()->is_internal)->toBeTrue();
});

it('supports soft deletes', function () {
    $comment = Comment::factory()->create();
    $comment->delete();

    expect(Comment::find($comment->id))->toBeNull()
        ->and(Comment::withTrashed()->find($comment->id))->not->toBeNull();
});

it('belongs to a ticket', function () {
    $ticket = Ticket::factory()->create();
    $comment = Comment::factory()->create(['ticket_id' => $ticket->id]);

    expect($comment->ticket->id)->toBe($ticket->id);
});

it('belongs to a user', function () {
    $user = User::factory()->create();
    $comment = Comment::factory()->create(['user_id' => $user->id]);

    expect($comment->user->id)->toBe($user->id);
});
