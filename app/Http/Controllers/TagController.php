<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Spatie\Tags\Tag;

class TagController extends Controller
{
    public function index(): View
    {
        $tags = Tag::query()->orderBy('order_column')->get();

        $ticketCounts = DB::table('taggables')
            ->select('tag_id', DB::raw('count(*) as count'))
            ->where('taggable_type', \App\Models\Ticket::class)
            ->groupBy('tag_id')
            ->pluck('count', 'tag_id');

        return view('tags.index', compact('tags', 'ticketCounts'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
        ]);

        Tag::findOrCreate($request->input('name'));

        return redirect()->route('tags.index')->with('success', 'Tag created.');
    }

    public function destroy(Tag $tag): RedirectResponse
    {
        $tag->delete();

        return redirect()->route('tags.index')->with('success', 'Tag deleted.');
    }
}
