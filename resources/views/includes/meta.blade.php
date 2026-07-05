@php
    $metaAttachment = $this->activeAttachment;
    $metaDimensions = $this->activeImageDimensions;
@endphp
<div class="flex flex-col justify-between p-0 m-0 w-full h-full">

    <div class="h-12 min-h-12 max-h-12 w-full">
        <div class="flex items-center justify-between px-4 h-full w-full">
            <span class="text-[11px] font-medium uppercase tracking-widest text-zinc-400 dark:text-zinc-500 select-none">Info</span>
            <div></div>
        </div>
    </div>

    <div class="grow border-b border-t border-zinc-200 dark:border-zinc-800 scrollY h-auto p-2">
        <div class="flex items-start justify-center p-0 m-0 w-full h-full">
            <div class="flex flex-col items-start justify-start w-full p-2 gap-y-3">

                @if (!$metaAttachment)
                <p class="w-full text-xs text-zinc-400 dark:text-zinc-500 text-center pt-6 select-none">Select an item to see its details.</p>
                @endif

                @if ($metaAttachment && str_starts_with($metaAttachment->file_type, 'image/'))
                <figure class="w-full mb-0">
                    <img src="{{ $metaAttachment->file_url }}?v={{ $this->cacheKey($metaAttachment->updated_at) }}" class="w-full object-cover rounded-lg ring-1 ring-zinc-950/10 dark:ring-white/10 shadow-sm" />
                    @if($metaAttachment->title)
                    <figcaption class="mt-2 rounded-md font-mono text-xs tracking-wide text-zinc-600 dark:text-zinc-300 overflow-hidden w-full py-1 px-1.5 hover:bg-zinc-200/60 dark:hover:bg-zinc-800 cursor-copy transition-colors" title="Click to copy" data-textcopy>{{ $metaAttachment->title }}</figcaption>
                    @endif
                </figure>
                @endif

                @if ($metaAttachment && str_starts_with($metaAttachment->file_type, 'video/'))
                <figure class="w-full mb-0">
                    <video src="{{ asset($metaAttachment->file_url) }}" class="w-full rounded-lg ring-1 ring-zinc-950/10 dark:ring-white/10 shadow-sm" controls></video>
                    @if($metaAttachment->title)
                    <figcaption class="mt-2 rounded-md font-mono text-xs tracking-wide text-zinc-600 dark:text-zinc-300 overflow-hidden w-full py-1 px-1.5 hover:bg-zinc-200/60 dark:hover:bg-zinc-800 cursor-copy transition-colors" title="Click to copy" data-textcopy>{{ $metaAttachment->title }}</figcaption>
                    @endif
                </figure>
                @endif

                @if ($metaAttachment && str_starts_with($metaAttachment->file_type, 'audio/'))
                <figure class="w-full mb-0">
                    <audio controls class="w-full">
                        <source src="{{ asset($metaAttachment->file_url) }}" type="{{ $metaAttachment->file_type }}">
                    </audio>
                    @if($metaAttachment->title)
                    <figcaption class="mt-2 rounded-md font-mono text-xs tracking-wide text-zinc-600 dark:text-zinc-300 overflow-hidden w-full py-1 px-1.5 hover:bg-zinc-200/60 dark:hover:bg-zinc-800 cursor-copy transition-colors" title="Click to copy" data-textcopy>{{ $metaAttachment->title }}</figcaption>
                    @endif
                </figure>
                @endif

                @if ($metaAttachment?->file_original_name)
                <div class="w-full">
                    <div class="px-1.5 text-[10px] font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500 select-none">Filename</div>
                    <div class="rounded-md font-mono text-xs tracking-wide text-zinc-700 dark:text-zinc-300 break-all w-full py-1 px-1.5 hover:bg-zinc-200/60 dark:hover:bg-zinc-800 cursor-copy transition-colors" title="Click to copy" data-textcopy>{{ $metaAttachment->file_original_name }}</div>
                </div>
                @endif

                @if ($metaAttachment?->file_size)
                <div class="w-full">
                    <div class="px-1.5 text-[10px] font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500 select-none">Size</div>
                    <div class="rounded-md font-mono text-xs tracking-wide text-zinc-700 dark:text-zinc-300 w-full py-1 px-1.5 hover:bg-zinc-200/60 dark:hover:bg-zinc-800 cursor-copy transition-colors" title="Click to copy" data-textcopy>{{ $this->formatBytes($metaAttachment->file_size) }}</div>
                </div>
                @endif

                @if ($metaAttachment?->file_type)
                <div class="w-full">
                    <div class="px-1.5 text-[10px] font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500 select-none">Type</div>
                    <div class="rounded-md font-mono text-xs tracking-wide text-zinc-700 dark:text-zinc-300 w-full py-1 px-1.5 hover:bg-zinc-200/60 dark:hover:bg-zinc-800 cursor-copy transition-colors" title="Click to copy" data-textcopy>{{ $this->formatMimeType($metaAttachment->file_type) }}</div>
                </div>
                @endif

                @if ($metaAttachment?->file_size && $metaDimensions)
                <div class="w-full">
                    <div class="px-1.5 text-[10px] font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500 select-none">Dimensions</div>
                    <div class="rounded-md font-mono text-xs tracking-wide text-zinc-700 dark:text-zinc-300 w-full py-1 px-1.5 hover:bg-zinc-200/60 dark:hover:bg-zinc-800 cursor-copy transition-colors" title="Click to copy" data-textcopy>{{ $metaDimensions['width'] }}&times;{{ $metaDimensions['height'] }}</div>
                </div>
                @endif

                @if ($metaAttachment?->created_at)
                <div class="w-full">
                    <div class="px-1.5 text-[10px] font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500 select-none">Created</div>
                    <div class="rounded-md font-mono text-xs tracking-wide text-zinc-700 dark:text-zinc-300 w-full py-1 px-1.5 hover:bg-zinc-200/60 dark:hover:bg-zinc-800 cursor-copy transition-colors" title="Click to copy" data-textcopy>{{ $metaAttachment->getCreatedAt() }}</div>
                </div>
                @endif

                @if ($metaAttachment?->updated_at)
                <div class="w-full">
                    <div class="px-1.5 text-[10px] font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500 select-none">Updated</div>
                    <div class="rounded-md font-mono text-xs tracking-wide text-zinc-700 dark:text-zinc-300 w-full py-1 px-1.5 hover:bg-zinc-200/60 dark:hover:bg-zinc-800 cursor-copy transition-colors" title="Click to copy" data-textcopy>{{ $metaAttachment->getUpdatedAt() }}</div>
                </div>
                @endif

            </div>
        </div>
    </div>

    <div class="h-12 min-h-12 max-h-12 w-full">
        <div class="flex items-center justify-start px-4 h-full w-full gap-x-2">
            @if($metaAttachment)
            <span class="text-[10px] text-zinc-400 dark:text-zinc-500 select-none">Click a value to copy it</span>
            @endif
        </div>
    </div>
</div>
