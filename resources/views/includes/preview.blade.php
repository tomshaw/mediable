@php $previewAttachment = $this->activeAttachment; @endphp
<div x-data="{ lightbox: false }" @class([
    "flex items-center justify-center overflow-hidden p-4 md:p-6 lg:p-8 m-0",
    ($previewAttachment && str_starts_with($previewAttachment->file_type, 'image/')) ? 'h-auto' : 'h-full'
])>
    @if ($previewAttachment)
        @if (str_starts_with($previewAttachment->file_type, 'image/'))
        <div class="flex items-center justify-center h-full w-full">
            <img src="{{ asset($previewAttachment->file_url) }}?v={{ $this->cacheKey($previewAttachment->updated_at) }}" class="object-contain rounded-lg shadow-lg ring-1 ring-zinc-950/10 dark:ring-white/10 cursor-zoom-in" data-id="{{ $previewAttachment->id }}" x-on:click="lightbox = true" title="Click to enlarge">
        </div>
        <div x-cloak x-show="lightbox" x-transition.opacity.duration.300ms x-on:click="lightbox = false" x-on:keydown.escape.window="lightbox = false" class="fixed inset-0 z-50 flex items-center justify-center bg-zinc-950/90 backdrop-blur-sm cursor-zoom-out p-4 md:p-8">
            <img src="{{ asset($previewAttachment->file_url) }}?v={{ $this->cacheKey($previewAttachment->updated_at) }}" class="max-w-full max-h-full object-contain rounded-lg shadow-2xl" alt="{{ $previewAttachment->file_original_name }}">
            <button type="button" x-on:click.stop="lightbox = false" class="absolute top-4 right-4 flex items-center justify-center w-9 h-9 rounded-full bg-white/10 text-white hover:bg-white/20 transition-colors cursor-pointer" title="Close (Esc)">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                    <path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" />
                </svg>
            </button>
        </div>
        @elseif (str_starts_with($previewAttachment->file_type, 'video/'))
        <div class="flex items-center justify-center h-full w-full">
            <video src="{{ asset($previewAttachment->file_url) }}" class="rounded-lg shadow-lg ring-1 ring-zinc-950/10 dark:ring-white/10" controls></video>
        </div>
        @elseif (str_starts_with($previewAttachment->file_type, 'audio/'))
        <div class="flex items-center justify-center h-full w-full">
            <audio controls class="w-full max-w-md">
                <source src="{{ asset($previewAttachment->file_url) }}" type="{{ $previewAttachment->file_type }}">
            </audio>
        </div>
        @endif
    @endif
</div>
