<div class="flex items-center justify-center h-auto p-4 md:p-6 lg:p-8 m-0">

  @if ($this->mimeTypeImage($this->fileType))
  <img src="{{ asset($this->fileUrl) }}" class="object-contain shadow border border-black">
  @elseif ($this->mimeTypeVideo($this->fileType))
  <video src="{{ asset($this->fileUrl) }}" controls></video>
  @elseif ($this->mimeTypeAudio($this->fileType))
  <audio controls>
    <source src="{{ asset($this->fileUrl) }}" type="{{ $this->fileType }}">
  </audio>
  @endif

</div>