<div class="flex items-center justify-between h-full py-0 px-4">
  <div class="flex items-center justify-start gap-2">
    @if (count($selected))
    <button wire:click="confirmDelete" class="group relative inline-flex items-center justify-center overflow-hidden rounded-md bg-[#555] py-1.5 px-3 text-xs font-normal text-white">
      <span class="absolute h-0 w-0 rounded-full bg-red-500 transition-all duration-300 group-hover:h-56 group-hover:w-32"></span>
      <span class="relative">[#]</span>
    </button>
    <button wire:click="clearSelected" class="group relative inline-flex items-center justify-center overflow-hidden rounded-md bg-[#555] py-1.5 px-3 text-xs font-normal text-white">
      <span class="absolute h-0 w-0 rounded-full bg-blue-500 transition-all duration-300 group-hover:h-56 group-hover:w-32"></span>
      <span class="relative">Clear Selected</span>
    </button>
    @endif
    <ul class="flex items-center justify-start ml-4 gap-x-2">
      @foreach($selected as $item)
      <li class="shadow-md cursor-pointer" wire:click="setActiveAttachment({{$item}})">
        <div @class(['border border-black overflow-hidden', ($item->id === $this->attachment->id) ? 'w-11 h-11' : 'w-9 h-9'])>
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
    <button wire:click="insertMedia" class="group relative inline-flex items-center justify-center overflow-hidden rounded-md bg-[#555] py-1.5 px-3 text-xs font-normal text-white">
      <span class="absolute h-0 w-0 rounded-full bg-blue-500 transition-all duration-300 group-hover:h-56 group-hover:w-32"></span>
      <span class="relative">Insert Selected</span>
    </button>
    @endif
  </div>
</div>