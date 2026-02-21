@props(['media', 'compact' => false, 'showDelete' => true])

@if ($compact)
    <div class="flex flex-wrap gap-2">
        @foreach ($media as $item)
            <button
                type="button"
                class="badge badge-outline badge-sm cursor-pointer hover:badge-primary transition-colors"
                data-preview-url="{{ $item->getTemporaryUrl() }}"
                data-preview-name="{{ $item->file_name }}"
                data-preview-type="{{ $item->mime_type }}"
                data-download-url="{{ route('media.download', $item) }}"
                x-data
                @click="window.dispatchEvent(new CustomEvent('open-preview', { detail: { url: $el.dataset.previewUrl, name: $el.dataset.previewName, mimeType: $el.dataset.previewType, downloadUrl: $el.dataset.downloadUrl } }))"
            >
                {{ $item->file_name }}
            </button>
        @endforeach
    </div>
@else
    <div class="space-y-1">
        @foreach ($media as $item)
            <div class="flex items-center justify-between gap-2 rounded-lg border border-base-200 px-3 py-2 text-sm">
                <button
                    type="button"
                    class="truncate text-left link link-hover min-w-0"
                    data-preview-url="{{ $item->getTemporaryUrl() }}"
                    data-preview-name="{{ $item->file_name }}"
                    data-preview-type="{{ $item->mime_type }}"
                    data-download-url="{{ route('media.download', $item) }}"
                    x-data
                    @click="window.dispatchEvent(new CustomEvent('open-preview', { detail: { url: $el.dataset.previewUrl, name: $el.dataset.previewName, mimeType: $el.dataset.previewType, downloadUrl: $el.dataset.downloadUrl } }))"
                >
                    <span>{{ $item->file_name }}</span>
                    <span class="text-base-content/40 ml-2">{{ number_format($item->size / 1024, 1) }} KB</span>
                </button>

                <div class="flex items-center gap-1 shrink-0">
                    <a href="{{ route('media.download', $item) }}" class="btn btn-xs btn-ghost" title="Download">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                    </a>
                    @if ($showDelete)
                        <form method="POST" action="{{ route('media.destroy', $item) }}" x-data @submit.prevent="if(confirm('Delete this file?')) $el.submit()">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-xs btn-ghost text-error">Delete</button>
                        </form>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@endif
