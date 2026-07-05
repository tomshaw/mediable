<div class="flex items-center justify-between h-full px-4 gap-4 border-t border-zinc-200 dark:border-zinc-800">

    {{-- Left: count + thumbnails --}}
    <div class="flex items-center gap-3 min-w-0">
        @if (count($selectedIds))
        <span class="shrink-0 inline-flex items-center gap-1.5 rounded-full bg-indigo-600 px-2.5 py-0.5 text-xs font-semibold text-white tabular-nums">
            {{ count($selectedIds) }}
            <span class="hidden sm:inline font-normal">selected</span>
        </span>

        <div class="shrink-0 w-px h-6 bg-zinc-200 dark:bg-zinc-700"></div>

        <div class="flex items-center gap-2 min-w-0">
            @foreach($this->selectedAttachments as $item)
            <div wire:key="footer-{{ $item->id }}" wire:click="setActiveAttachment({{ $item->id }})" @class([
                'shrink-0 cursor-pointer overflow-hidden rounded-md transition-all duration-200 bg-white dark:bg-zinc-800',
                ($activeId && $item->id === $activeId)
                    ? 'w-11 h-11 ring-2 ring-indigo-500'
                    : 'w-9 h-9 ring-1 ring-zinc-950/10 dark:ring-white/10 hover:ring-zinc-400 dark:hover:ring-zinc-500'
            ])>
                @if (str_starts_with($item->file_type, 'image/'))
                <img src="{{ $item->file_url }}?v={{ $this->cacheKey($item->updated_at) }}" class="w-full h-full object-cover" alt="{{ $item->title }}" />
                @elseif (str_starts_with($item->file_type, 'video/'))
                <img src="{{ asset("vendor/mediable/images/video.png") }}" class="w-full h-full object-cover" alt="{{ $item->title }}" />
                @elseif (str_starts_with($item->file_type, 'audio/'))
                <img src="{{ asset("vendor/mediable/images/audio.png") }}" class="w-full h-full object-cover" alt="{{ $item->title }}" />
                @else
                <img src="{{ asset("vendor/mediable/images/file.png") }}" class="w-full h-full object-cover" alt="{{ $item->title }}" />
                @endif
            </div>
            @endforeach
        </div>
        @else
        <span class="text-xs text-zinc-400 dark:text-zinc-500 select-none">Select items to attach them</span>
        @endif
    </div>

    {{-- Right: actions --}}
    @if (count($selectedIds))
    <div class="flex items-center shrink-0 gap-1.5">
        <button wire:click="clearSelected" wire:island="selection" class="inline-flex items-center gap-1 h-8 rounded-lg px-2.5 text-xs font-medium text-zinc-600 hover:bg-zinc-200/70 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-100 cursor-pointer transition-colors" title="Clear selection">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-3.5 h-3.5">
                <path d="M5.28 4.22a.75.75 0 0 0-1.06 1.06L6.94 8l-2.72 2.72a.75.75 0 1 0 1.06 1.06L8 9.06l2.72 2.72a.75.75 0 1 0 1.06-1.06L9.06 8l2.72-2.72a.75.75 0 0 0-1.06-1.06L8 6.94 5.28 4.22Z" />
            </svg>
            Clear
        </button>

        <button wire:click="confirmDelete" class="inline-flex items-center gap-1 h-8 rounded-lg px-2.5 text-xs font-medium text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950/40 cursor-pointer transition-colors" title="Delete selected attachments">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-3.5 h-3.5">
                <path fill-rule="evenodd" d="M5 3.25V4H2.75a.75.75 0 0 0 0 1.5h.3l.815 8.15A1.5 1.5 0 0 0 5.357 15h5.285a1.5 1.5 0 0 0 1.493-1.35l.815-8.15h.3a.75.75 0 0 0 0-1.5H11v-.75A2.25 2.25 0 0 0 8.75 1h-1.5A2.25 2.25 0 0 0 5 3.25Zm2.25-.75a.75.75 0 0 0-.75.75V4h3v-.75a.75.75 0 0 0-.75-.75h-1.5ZM6.05 6a.75.75 0 0 1 .787.713l.275 5.5a.75.75 0 0 1-1.498.075l-.275-5.5A.75.75 0 0 1 6.05 6Zm3.9 0a.75.75 0 0 1 .712.787l-.275 5.5a.75.75 0 0 1-1.498-.075l.275-5.5A.75.75 0 0 1 9.95 6Z" clip-rule="evenodd" />
            </svg>
            Delete
        </button>

        <div class="shrink-0 w-px h-6 bg-zinc-200 dark:bg-zinc-700 mx-1"></div>

        <button wire:click="insertMedia" class="inline-flex items-center gap-1.5 h-8 rounded-lg bg-indigo-600 px-3.5 text-xs font-medium text-white hover:bg-indigo-500 cursor-pointer transition-colors shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-3.5 h-3.5">
                <path fill-rule="evenodd" d="M8 2a.75.75 0 0 1 .75.75V7h4.25a.75.75 0 0 1 0 1.5H8.75v4.25a.75.75 0 0 1-1.5 0V8.5H3a.75.75 0 0 1 0-1.5h4.25V2.75A.75.75 0 0 1 8 2Z" clip-rule="evenodd" />
            </svg>
            Attach selected
        </button>
    </div>
    @endif

</div>
