<div class="flex items-center justify-between h-full py-0 px-4">
  <div class="flex items-center justify-start gap-2">
    @if (count($selected))
    <button type="button" class="relative flex items-center justify-center px-3 py-1.5 bg-[#555] text-white rounded-md text-xs font-normal cursor-pointer transition-all duration-100 ease-in hover:text-white focus:outline-none" wire:click="deleteSelected()" title="Delete selected">
      [#]
    </button>
    <button type="button" class="relative flex items-center justify-center px-3 py-1.5 bg-[#555] text-white rounded-md text-xs font-normal cursor-pointer transition-all duration-100 ease-in hover:text-white focus:outline-none" wire:click="clearSelected()" title="Clear selected">
      Clear Selected
    </button>
    @endif
    <ul class="flex items-center justify-start ml-4 gap-x-2">
      @foreach($selected as $item)
      <li class="shadow-md cursor-pointer" wire:click="setActiveAttachment({{$item}})">
        <div @class(['border border-black w-10 h-10 overflow-hidden', in_array($item->id, array_column($this->selected, 'id')) ? 'border-black' : 'border-black'])>
          @if ($this->mimeTypeImage($item['file_type']))
          <img src="{{ $item['file_url'] }}?id={{ $uniqueId }}" class="w-full h-full object-cover" alt="{{ $item['title'] }}" />
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
    <button type="button" class="relative flex items-center justify-center px-3 py-1.5 bg-[#555] text-white rounded-md text-xs font-normal cursor-pointer transition-all duration-100 ease-in hover:text-white focus:outline-none" wire:click="insertMedia()">Insert Attachments</button>
    @endif
  </div>
</div>