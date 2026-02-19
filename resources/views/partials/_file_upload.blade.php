{{--
  Reusable file upload partial.
  Props: $label (string), $existingMedia (MediaCollection), $collection (string for display only)
--}}
<div class="form-control">
    <label class="label">
        <span class="label-text font-medium">{{ $label ?? 'Attachments' }}</span>
        <span class="label-text-alt">Max 20 MB per file</span>
    </label>

    <input
        type="file"
        name="files[]"
        multiple
        class="file-input file-input-bordered w-full"
        accept="*/*"
    >

    @if (isset($existingMedia) && $existingMedia->isNotEmpty())
        <div class="mt-3 space-y-1">
            @foreach ($existingMedia as $media)
                <div class="flex items-center justify-between rounded-lg border border-base-200 px-3 py-2 text-sm">
                    <div class="flex items-center gap-2 min-w-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0 text-base-content/50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                        </svg>
                        <a href="{{ $media->getUrl() }}" target="_blank" class="link link-hover truncate">
                            {{ $media->file_name }}
                        </a>
                        <span class="text-base-content/40 shrink-0">{{ number_format($media->size / 1024, 1) }} KB</span>
                    </div>
                    <form method="POST" action="{{ route('media.destroy', $media) }}" x-data @submit.prevent="if(confirm('Delete this file?')) $el.submit()">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-xs btn-ghost text-error ml-2">Delete</button>
                    </form>
                </div>
            @endforeach
        </div>
    @endif
</div>
