@if ($data->count())
<ul class="flex flex-wrap -mx-1 p-0 w-full">
  @foreach($data as $item)
  <li class="attachment relative flex bg-[#e5e7eb] m-0 p-0 cursor-pointer list-none text-center select-none border-r border-gray-300" wire:click="toggleAttachment({{$item->id}})" style="width: {{$columnWidths[$defaultColumnWidth]}}%;">
    <div class="relative cursor-pointer py-4 md:py-8 lg:py-12 xl:py-16 px-4 md:px-8 flex items-center justify-center min-w-full">

      <div class="hidden lg:block absolute top-[1px] left-0">
        @if(in_array($item->id, array_column($this->selected, 'id')))
        <span class="text-left text-xs font-bold text-blue-500 bg-transparent py-1 px-2">({{$item->id}})</span>
        @else
        <span class="text-left text-xs font-bold text-gray-500 bg-transparent py-1 px-2">{{$item->id}}</span>
        @endif
      </div>

      <div class="hidden lg:block absolute top-0 right-0">
        <span class="text-right text-xs font-normal text-[#777] bg-transparent py-1 px-2">{{$this->formatBytes($item->file_size)}} &dash; {{ strtoupper(collect(explode('/', $item->file_type))->last()) }}</span>
      </div>

      @if ($this->mimeTypeImage($item->file_type))
      <img src="{{ asset($item->file_url) }}?id={{ $uniqueId }}" class="attachment__item object-contain shadow-md border border-black" alt="{{ $item->file_original_name }}">
      @elseif ($this->mimeTypeVideo($item->file_type))
      <video src="{{ asset($item->file_url) }}" class="attachment__item" alt="{{ $item->file_original_name }}" controls></video>
      @elseif ($this->mimeTypeAudio($item->file_type))
      <div class="relative overflow-hidden">

        <audio class="hidden" id="audioPlayer{{ $item->id }}">
          <source src="{{ asset($item->file_url) }}" type="{{ $item->file_type }}">
        </audio>

        <button wire:click.stop="$dispatch('audio.start', { id: {{ $item->id }} })" id="playIcon{{ $item->id }}" @class(['w-12 h-12 bg-[#444] rounded-full items-center justify-center cursor-pointer', ($item->id === $this->audioElementId) ? 'hidden' : 'flex'])>
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6 text-white">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
          </svg>
        </button>

        <button wire:click.stop="$dispatch('audio.pause', { id: {{ $item->id }} })" id="pauseIcon{{ $item->id }}" @class(['bg-white bg-opacity-10 w-12 h-12 justify-between items-end p-2 box-border cursor-pointer gap-x-[1px]', ($item->id === $this->audioElementId) ? 'flex' : 'hidden'])>
          <span class="audio-animation inline-block bg-[#444] w-1/3 h-[60%]" style="animation-delay: 0;"></span>
          <span class="audio-animation inline-block bg-[#444] w-1/3 h-[30%]" style="animation-delay: -2.2s;"></span>
          <span class="audio-animation inline-block bg-[#444] w-1/3 h-[75%]" style="animation-delay: -3.7s"></span>
        </button>

      </div>
      @else
      <div class="relative object-contain">
        <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center cursor-pointer">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6 text-white">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 2v20h12V8l-6-6zm6 4v4h6"></path>
          </svg>
        </div>
      </div>
      @endif

      @if ($item->title)
      <div class="absolute inset-x-0 bottom-0 bg-[#444] overflow-hidden max-h-full whitespace-nowrap text-left text-xs font-normal px-1.5">
        <div class="absolute inset-y-0 left-0 h-full w-0 bg-blue-500 z-0" id="audioProgress{{$item->id}}"></div>
        <span class="inline-block align-middle text-white text-xs font-light py-1 relative z-10">{!! $item->title !!}</span>
      </div>
      @endif

    </div>
  </li>
  @endforeach
</ul>
@endif