<div @class([
    "flex items-center justify-center overflow-hidden p-4 md:p-6 lg:p-8 m-0",
    ($attachment && $this->mimeTypeImage($attachment->file_type)) ? 'h-auto' : 'h-full'
])>
    @if ($attachment)
        @if ($this->mimeTypeImage($attachment->file_type))
        <div class="flex items-center justify-center h-full w-full">
            <img src="{{ asset($attachment->file_url) }}?id={{ $uniqueId }}" class="object-contain shadow-md" data-id="{{$attachment->id}}">
        </div>
        @elseif ($this->mimeTypeVideo($attachment->file_type))
        <div class="flex items-center justify-center h-full w-full">
            <video src="{{ asset($attachment->file_url) }}" controls></video>
        </div>
        @elseif ($this->mimeTypeAudio($attachment->file_type))
        <div class="flex items-center justify-center h-full w-full">
            <audio controls>
                <source src="{{ asset($attachment->file_url) }}" type="{{ $attachment->file_type }}">
            </audio>
        </div>
        @endif
    @endif
</div>
