<div class="flex items-center justify-center overflow-hidden p-4 md:p-6 lg:p-8 m-0 h-full bg-pattern">
  @if ($this->mimeTypeImage($this->model->fileType))
  <div class="flex items-center justify-center" style="width: 40%;">
    <img src="{{ asset($this->model->fileUrl) }}?id={{ $uniqueId }}" class="object-contain shadow-md max-w-full" data-id={{$this->model->id}}>
  </div>
  @elseif ($this->mimeTypeVideo($this->model->fileType))
  <div class="flex items-center justify-center h-full w-full">
    <video src="{{ asset($this->model->fileUrl) }}" controls></video>
  </div>
  @elseif ($this->mimeTypeAudio($this->model->fileType))
  <div class="flex items-center justify-center h-full w-full">
    <audio controls>
      <source src="{{ asset($this->model->fileUrl) }}" type="{{ $this->model->fileType }}">
    </audio>
  </div>
  @endif
</div>