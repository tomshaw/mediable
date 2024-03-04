<div @class(["flex items-center justify-center overflow-hidden p-4 md:p-6 lg:p-8 m-0", ($this->mimeTypeImage($this->fileType) && !$editorMode) ? 'h-auto' : 'h-full', $editorMode ? 'bg-pattern' : ''])>
  @if ($this->mimeTypeImage($this->fileType))
  <div class="flex items-center justify-center" style="width: {{ $editorMode ? '40' : '100' }}%;">
    <img src="{{ asset($this->fileUrl) }}?id={{ $uniqueId }}" class="object-contain shadow" data-id={{$this->modelId}} style="{{ $editorMode ? 'max-width: 100%;' : '' }}">
  </div>
  @elseif ($this->mimeTypeVideo($this->fileType))
  <div class="flex items-center justify-center h-full w-full">
    <video src="{{ asset($this->fileUrl) }}" controls></video>
  </div>
  @elseif ($this->mimeTypeAudio($this->fileType))
  <div class="flex items-center justify-center h-full w-full">
    <audio controls>
      <source src="{{ asset($this->fileUrl) }}" type="{{ $this->fileType }}">
    </audio>
  </div>
  @endif
</div>