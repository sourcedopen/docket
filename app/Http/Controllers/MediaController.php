<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaController extends Controller
{
    public function download(Media $media): RedirectResponse
    {
        $url = Storage::disk($media->disk)->temporaryUrl(
            $media->getPath(),
            now()->addMinutes(5),
            ['ResponseContentDisposition' => 'attachment; filename="'.rawurlencode($media->file_name).'"']
        );

        return redirect()->away($url);
    }

    public function destroy(Media $media): RedirectResponse
    {
        $media->delete();

        return back()->with('success', 'File deleted.');
    }
}
