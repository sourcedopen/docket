<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaController extends Controller
{
    public function destroy(Media $media): RedirectResponse
    {
        $media->delete();

        return back()->with('success', 'File deleted.');
    }
}
