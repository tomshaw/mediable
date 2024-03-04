<div @class(["flex items-center justify-center p-4 md:p-6 lg:p-8 m-0", ($this->mimeTypeImage($this->fileType) && !$destructive) ? 'h-auto' : 'h-full', $destructive ? 'bg-pattern' : ''])>
  @if ($this->mimeTypeImage($this->fileType))
  <div class="flex items-center justify-center" style="width: {{ $destructive ? '50' : $imageWidth }}%;">
    <img src="{{ asset($this->fileUrl) }}?id={{ $uniqueId }}" class="object-contain shadow" data-id={{$this->modelId}} style="{{ $destructive ? 'max-width: 100%;' : '' }}">
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