<div class="flex items-center justify-between h-full py-0 px-4">

  @if(!$uploadMode)
  <div class="flex items-center justify-start gap-2">
    @if (count($selected))
    <button type="button" class="relative flex items-center justify-center px-3 py-1.5 bg-[#555] text-white rounded-md text-xs font-normal cursor-pointer transition-all duration-100 ease-in hover:text-white focus:outline-none" wire:click="deleteSelected()">Delete Selected</button>
    <button type="button" class="relative flex items-center justify-center px-3 py-1.5 bg-[#555] text-white rounded-md text-xs font-normal cursor-pointer transition-all duration-100 ease-in hover:text-white focus:outline-none" wire:click="clearSelected()">Clear Selected</button>
    @endif
    <ul class="flex items-center justify-start ml-4 gap-x-2">
      @foreach($selected as $item)
      <li @class(['cursor-pointer', 'shadow-[inset_0px_0px_0px_3px_#fff,0px_0px_4px_2px_#4299e1]' => $item['id'] == $this->modelId]) data-id={{$item['id']}} wire:click="setActiveAttachment({{$item}})">
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
    <button type="button" class="relative flex items-center justify-center px-3 py-1.5 bg-[#555] text-white rounded-md text-xs font-normal cursor-pointer transition-all duration-100 ease-in hover:text-white focus:outline-none" wire:click="insertMedia()">Submit Attachments</button>
    @endif
  </div>
  @endif

</div>