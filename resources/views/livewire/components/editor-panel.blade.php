<div class="flex items-center justify-center overflow-hidden p-4 md:p-6 lg:p-8 m-0 h-full bg-pattern">
    @if ($attachment && $this->mimeTypeImage($attachment->file_type))
    <div class="flex items-center justify-center">
        <img src="{{ asset($attachment->file_url) }}?id={{ $uniqueId }}" class="object-contain shadow-md max-h-96">
    </div>
    @endif
</div>
