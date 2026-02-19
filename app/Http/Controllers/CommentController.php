<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;

class CommentController extends Controller
{
    public function store(StoreCommentRequest $request, Ticket $ticket): RedirectResponse
    {
        $ticket->comments()->create([
            'user_id' => auth()->id(),
            'body' => $request->validated('body'),
            'type' => $request->validated('type'),
        ]);

        return redirect()->route('tickets.show', $ticket)->with('success', 'Comment added.');
    }

    public function destroy(Ticket $ticket, Comment $comment): RedirectResponse
    {
        abort_unless($comment->ticket_id === $ticket->id, 404);
        abort_unless($comment->user_id === auth()->id(), 403);

        $comment->delete();

        return redirect()->route('tickets.show', $ticket)->with('success', 'Comment deleted.');
    }
}
