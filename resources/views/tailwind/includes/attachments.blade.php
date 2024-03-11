@if ($data->count())
<ul class="flex flex-wrap -mx-1 p-0 pl-1 w-full">
  @foreach($data as $item)
  <li @class(['attachment relative flex m-0 p-0 cursor-pointer list-none text-center select-none border-b border-r border-black', in_array($item->id, array_column($this->selected, 'id')) ? 'bg-[#cbcbcb]' : 'bg-[#555]']) id="attachment-id-{{$item->id}}" wire:click="toggleAttachment({{$item->id}})" style="width: {{$columnWidths[$defaultColumnWidth]}}%;">
    <div class="relative cursor-pointer flex items-center justify-center min-w-full" style="padding: {{ $this->normalizeColumnPadding($columnWidths[$defaultColumnWidth]) }}rem;">

      <div class="hidden lg:block absolute top-[1px] left-0">
        @if(in_array($item->id, array_column($this->selected, 'id')))
        <span class="text-left font-light text-black bg-transparent py-1 px-2" style="font-size: 10px;">{{$item->id}}</span>
        @else
        <span class="text-left font-light text-white bg-transparent py-1 px-2" style="font-size: 10px;">{{$item->id}}</span>
        @endif
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

        <button wire:click.stop="$dispatch('audio.start', { id: {{ $item->id }} })" id="playIcon{{ $item->id }}" @class(['w-12 h-12 bg-[#555] rounded-full items-center justify-center cursor-pointer', ($item->id === $this->audioElementId) ? 'hidden' : 'flex'])>
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6 text-white">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
          </svg>
        </button>

        <button wire:click.stop="$dispatch('audio.pause', { id: {{ $item->id }} })" id="pauseIcon{{ $item->id }}" @class(['bg-white bg-opacity-10 w-12 h-12 justify-between items-end p-2 box-border cursor-pointer gap-x-[1px]', ($item->id === $this->audioElementId) ? 'flex' : 'hidden'])>
          <span class="audio-animation inline-block bg-[#555] w-1/3 h-[60%]" style="animation-delay: 0;"></span>
          <span class="audio-animation inline-block bg-[#555] w-1/3 h-[30%]" style="animation-delay: -2.2s;"></span>
          <span class="audio-animation inline-block bg-[#555] w-1/3 h-[75%]" style="animation-delay: -3.7s"></span>
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


    </div>
  </li>
  @endforeach
</ul>
@endif