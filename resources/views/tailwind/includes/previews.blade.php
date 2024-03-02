<div @class(["flex items-center justify-center p-4 md:p-6 lg:p-8 m-0", $this->mimeTypeImage($this->fileType) ? 'h-auto' : 'h-full'])>
  @if ($this->mimeTypeImage($this->fileType))
  <div class="flex items-center justify-center" style="width: {{ $imageWidth }}%;">
    <img src="{{ asset($this->fileUrl) }}" class="object-contain shadow">
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