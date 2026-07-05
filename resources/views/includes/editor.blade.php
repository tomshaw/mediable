@php $editorAttachment = $this->editorAttachment; @endphp
<div class="relative flex items-center justify-center overflow-hidden p-4 md:p-6 lg:p-8 m-0 h-full bg-pattern">
    @if ($editorAttachment && str_starts_with($editorAttachment->file_type, 'image/'))
    <div class="flex items-center justify-center">
        <img src="{{ asset($editorAttachment->file_url) }}?v={{ $this->cacheKey($editorAttachment->updated_at) }}.{{ $editorVersion }}" class="object-contain shadow-md max-h-96">
    </div>
    @endif
</div>
