<div class="flex items-center justify-between h-full py-0 px-4">

  @if(!$uploadMode)
  <div class="flex items-center justify-start gap-2">
    @if (count($selected))
    <button type="button" class="btn" wire:click="deleteSelected()">Delete Selected</button>
    <button type="button" class="btn" wire:click="clearSelected()">Clear Selected</button>
    @endif
    <ul class="flex items-center justify-start ml-4 gap-x-2">
      @foreach($selected as $item)
      <li @class(['shadow-[inset_0_0_0_1px_#fff,_inset_0_0_0_3px_#00b5d2]'=> $item['id'] == $this->modelId]) data-id={{$item['id']}}>
        <div class="border border-black shadow-md w-10 h-10 overflow-hidden">
          @if ($this->mimeTypeImage($item['file_type']))
          <img src="{{ $item['file_url'] }}" class="w-full h-full object-cover" alt="{{ $item['title'] }}" />
          @elseif ($this->mimeTypeVideo($item['file_type']))
          <img src="{{ asset("vendor/mediable/images/video.png") }}" class="w-full h-full object-cover" alt="{{ $item['title'] }}" />
          @elseif ($this->mimeTypeAudio($item['file_type']))
          <img src="{{ asset("vendor/mediable/images/audio.png") }}" class="w-full h-full object-cover" alt="{{ $item['title'] }}" />
          @else
          <img src="{{ asset("vendor/mediable/images/file.png") }}" class="w-full h-full object-cover" alt="{{ $item['title'] }}" />
          @endif
        </div>
      </li>
      @endforeach
    </ul>
  </div>
  <div class="flex items-center justify-start gap-2">
    @if (count($selected))
    <button type="button" class="btn" wire:click="insertMedia()">Add Attachments</button>
    @endif
  </div>
  @endif

</div>