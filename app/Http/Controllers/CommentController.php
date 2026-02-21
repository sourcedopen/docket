<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    public function store(StoreCommentRequest $request, Ticket $ticket): RedirectResponse
    {
        DB::transaction(function () use ($request, $ticket) {
            $comment = $ticket->comments()->create([
                'user_id' => auth()->id(),
                'body' => $request->validated('body'),
                'type' => $request->validated('type'),
                'commented_at' => $request->validated('commented_at'),
            ]);

            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $comment->addMedia($file)->toMediaCollection('attachments');
                }
            }
        });

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
