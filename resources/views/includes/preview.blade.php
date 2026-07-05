@php $previewAttachment = $this->activeAttachment; @endphp
<div @class([
    "flex items-center justify-center overflow-hidden p-4 md:p-6 lg:p-8 m-0",
    ($previewAttachment && str_starts_with($previewAttachment->file_type, 'image/')) ? 'h-auto' : 'h-full'
])>
    @if ($previewAttachment)
        @if (str_starts_with($previewAttachment->file_type, 'image/'))
        <div class="flex items-center justify-center h-full w-full">
            <img src="{{ asset($previewAttachment->file_url) }}?v={{ $this->cacheKey($previewAttachment->updated_at) }}" class="object-contain shadow-md" data-id="{{ $previewAttachment->id }}">
        </div>
        @elseif (str_starts_with($previewAttachment->file_type, 'video/'))
        <div class="flex items-center justify-center h-full w-full">
            <video src="{{ asset($previewAttachment->file_url) }}" controls></video>
        </div>
        @elseif (str_starts_with($previewAttachment->file_type, 'audio/'))
        <div class="flex items-center justify-center h-full w-full">
            <audio controls>
                <source src="{{ asset($previewAttachment->file_url) }}" type="{{ $previewAttachment->file_type }}">
            </audio>
        </div>
        @endif
    @endif
</div>
