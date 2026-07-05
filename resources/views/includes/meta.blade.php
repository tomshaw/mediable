@php
    $metaAttachment = $this->activeAttachment;
    $metaDimensions = $this->activeImageDimensions;
@endphp
<div class="flex flex-col justify-between p-0 m-0 w-full h-full">

    <div class="bg-gray-200 h-10 min-h-10 max-h-10 xl:h-11 xl:min-h-11 xl:max-h-11 2xl:h-12 2xl:min-h-12 2xl:max-h-12 w-full">
        <div class="flex items-center justify-between px-4 h-full w-full">
            <div></div>
            <div></div>
        </div>
    </div>

    <div class="grow border-b border-t border-gray-300 scrollY h-auto p-2">
        <div class="flex items-start justify-center p-0 m-0 w-full h-full">
            <div class="flex flex-col items-start justify-start w-full p-3 gap-y-1.5">

                @if ($metaAttachment && str_starts_with($metaAttachment->file_type, 'image/'))
                <figure class="w-full mb-0">
                    <img src="{{ $metaAttachment->file_url }}?v={{ $this->cacheKey($metaAttachment->updated_at) }}" class="w-full object-cover shadow rounded" />
                    <figcaption class="rounded font-mono text-xs tracking-wider text-neutral-50 overflow-hidden w-full mt-3 py-1.5 px-2 bg-neutral-900 hover:bg-neutral-950 cursor-copy" data-textcopy>{{ $metaAttachment->title }}</figcaption>
                </figure>
                @endif

                @if ($metaAttachment && str_starts_with($metaAttachment->file_type, 'video/'))
                <figure class="w-full mb-0">
                    <video src="{{ asset($metaAttachment->file_url) }}" controls></video>
                    <figcaption class="rounded font-mono text-xs tracking-wider text-neutral-50 overflow-hidden w-full mt-3 py-1.5 px-2 bg-neutral-900 hover:bg-neutral-950 cursor-copy" data-textcopy>{{ $metaAttachment->title }}</figcaption>
                </figure>
                @endif

                @if ($metaAttachment && str_starts_with($metaAttachment->file_type, 'audio/'))
                <figure class="w-full mb-0">
                    <audio controls class="w-55.75 mb-2">
                        <source src="{{ asset($metaAttachment->file_url) }}" type="{{ $metaAttachment->file_type }}">
                    </audio>
                    <figcaption class="rounded font-mono text-xs tracking-wider text-neutral-50 overflow-hidden w-full mt-3 py-1.5 px-2 bg-neutral-900 hover:bg-neutral-950 cursor-copy" data-textcopy>{{ $metaAttachment->title }}</figcaption>
                </figure>
                @endif

                @if ($metaAttachment?->file_original_name)
                <div class="rounded font-mono text-xs tracking-wider text-neutral-50 overflow-hidden w-full py-1.5 px-2 bg-neutral-900 hover:bg-neutral-950 cursor-copy" data-textcopy>
                    {{ $metaAttachment->file_original_name }}
                </div>
                @endif

                @if ($metaAttachment?->file_size)
                <div class="rounded font-mono text-xs tracking-wider text-neutral-50 overflow-hidden w-full py-1.5 px-2 bg-neutral-900 hover:bg-neutral-950 cursor-copy" data-textcopy>
                    {{ $this->formatBytes($metaAttachment->file_size) }}
                </div>
                @endif

                @if ($metaAttachment?->file_type)
                <div class="rounded font-mono text-xs tracking-wider text-neutral-50 overflow-hidden w-full py-1.5 px-2 bg-neutral-900 hover:bg-neutral-950 cursor-copy" data-textcopy>
                    {{ $this->formatMimeType($metaAttachment->file_type) }}
                </div>
                @endif

                @if ($metaAttachment?->file_size && $metaDimensions)
                <div class="rounded font-mono text-xs tracking-wider text-neutral-50 overflow-hidden w-full py-1.5 px-2 bg-neutral-900 hover:bg-neutral-950 cursor-copy" data-textcopy>
                    {{ $metaDimensions['width'] }}&times;{{ $metaDimensions['height'] }}
                </div>
                @endif

                @if ($metaAttachment?->created_at)
                <div class="rounded font-mono text-xs tracking-wider text-neutral-50 overflow-hidden w-full py-1.5 px-2 bg-neutral-900 hover:bg-neutral-950 cursor-copy" data-textcopy>
                    {{ $metaAttachment->getCreatedAt() }}
                </div>
                @endif

                @if ($metaAttachment?->updated_at)
                <div class="rounded font-mono text-xs tracking-wider text-neutral-50 overflow-hidden w-full py-1.5 px-2 bg-neutral-900 hover:bg-neutral-950 cursor-copy" data-textcopy>
                    {{ $metaAttachment->getUpdatedAt() }}
                </div>
                @endif

            </div>
        </div>
    </div>

    <div class="bg-gray-200 h-10 min-h-10 max-h-10 xl:h-11 xl:min-h-11 xl:max-h-11 2xl:h-12 2xl:min-h-12 2xl:max-h-12 w-full">
        <div class="flex items-center justify-start px-4 h-full w-full gap-x-2"></div>
    </div>
</div>
