<div @class(["flex items-center justify-center overflow-hidden p-4 md:p-6 lg:p-8 m-0", ($this->mimeTypeImage($this->attachment->file_type)) ? 'h-auto' : 'h-full'])>
  @if ($this->mimeTypeImage($this->attachment->file_type))
  <div class="flex items-center justify-center h-full w-full">
    <img src="{{ asset($this->attachment->file_url) }}?id={{ $uniqueId }}" class="object-contain shadow-md" data-id="{{$this->attachment->id}}">
  </div>
  @elseif ($this->mimeTypeVideo($this->attachment->file_type))
  <div class="flex items-center justify-center h-full w-full">
    <video src="{{ asset($this->attachment->file_url) }}" controls></video>
  </div>
  @elseif ($this->mimeTypeAudio($this->attachment->file_type))
  <div class="flex items-center justify-center h-full w-full">
    <audio controls>
      <source src="{{ asset($this->attachment->file_url) }}" type="{{ $this->attachment->file_type }}">
    </audio>
  </div>
  @endif
</div>