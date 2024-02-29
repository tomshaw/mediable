<div class="flex items-center justify-center p-0 m-0 w-full">

  @if($tableMode)
  <div class="overflow-hidden w-full">
    <table class="border border-collapse">
      @if ($data->count())
      <tbody>
        @foreach($data as $item)
        <tr @class([ 'selected'=> in_array($item->id, array_column($this->selected, 'id')), 'details' => $item->id == $this->modelId])>
          <td class="center">
            <span class="whitespace-nowrap">{{ $item->id }}</span>
          </td>
          <td class="text-left relative">
            <span class="whitespace-nowrap font-medium">{{ $item->title }}</span>
          </td>
          <td class="text-left">
            <span class="whitespace-nowrap">{{ $item->file_original_name }}</span>
          </td>
          <td>
            <span class="whitespace-nowrap">{{ $item->file_type }}</span>
          </td>
          <td>
            <span class="whitespace-nowrap">{{ $this->formatBytes($item->file_size) }}</span>
          </td>
          <td>
            <span class="whitespace-nowrap">{{ $item->created_at }}</span>
          </td>
          <td>
            <span class="whitespace-nowrap">{{ $item->updated_at }}</span>
          </td>
          <td>
            <div class="flex items-center justify-center gap-x-2">
              <button class="relative flex items-center justify-center px-4 py-1.5 gap-x-2 bg-[#555] text-white rounded-full text-xs font-normal cursor-pointer transition-all duration-100 ease-in" wire:click="toggleAttachment({{$item->id}})">Toggle</button>
              <button class="relative flex items-center justify-center px-4 py-1.5 gap-x-2 bg-[#555] text-white rounded-full text-xs font-normal cursor-pointer transition-all duration-100 ease-in" wire:click.prevent="deleteAttachment({{$item->id}})">Delete</button>
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
      @endif
    </table>
  </div>

  @elseif($thumbMode)

  <ul class="flex flex-wrap m-0 p-0 w-full">

    @if ($data->count())
    @foreach($data as $item)
    <li @class([ 'relative flex m-0 p-2 cursor-pointer list-none text-center select-none w-[20%]' , 'selected shadow-[inset_0_0_0_2px_#fff,_inset_0_0_0_7px_#00b5d2]'=> in_array($item->id, array_column($this->selected, 'id')), 'details' => $item->id == $this->modelId])
      wire:click="toggleAttachment({{$item->id}})"
      style="width: {{$columnWidths[$defaultColumnWidth]}}%;">

      <div class="relative bg-[#e5e7eb] cursor-pointer py-4 md:py-8 lg:py-12 xl:py-16 px-4 md:px-8 flex items-center justify-center min-w-full">

        <div class="hidden lg:block absolute top-[1px] left-0">
          <span class="text-left text-xs font-normal text-[#777] bg-transparent py-1 px-2">{{$item->id}}</span>
        </div>

        @if ($this->mimeTypeImage($item->file_type))
        <img src="{{ asset($item->file_url) }}" class="object-contain shadow border border-black" alt="{{ $item->file_original_name }}">
        @elseif ($this->mimeTypeVideo($item->file_type))
        <video src="{{ asset($item->file_url) }}" alt="{{ $item->file_original_name }}" controls></video>
        @elseif ($this->mimeTypeAudio($item->file_type))
        <div class="relative object-contain overflow-hidden">

          <audio class="hidden" id="audioPlayer{{ $item->id }}">
            <source src="{{ asset($item->file_url) }}" type="{{ $item->file_type }}">
          </audio>

          <button wire:click.stop="$dispatch('audio.start', { id: {{ $item->id }} })" id="playIcon{{ $item->id }}" @class([ 'w-12 h-12 bg-[#444] rounded-full flex items-center justify-center cursor-pointer'=> $item->id != $this->audioElementId,
            'w-12 h-12 bg-[#444] rounded-full hidden items-center justify-center cursor-pointer' => $item->id == $this->audioElementId
            ])>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6 text-white">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
          </button>

          <button wire:click.stop="$dispatch('audio.pause', { id: {{ $item->id }} })" id="pauseIcon{{ $item->id }}" @class([ 'bg-white bg-opacity-10 w-12 h-12 rounded-md hidden justify-between items-end p-2 box-border cursor-pointer gap-x-[1px]'=> $item->id != $this->audioElementId,
            'bg-white bg-opacity-10 w-12 h-12 flex justify-between items-end p-2 box-border cursor-pointer gap-x-[1px]' => $item->id == $this->audioElementId
            ])>
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
        <div class="hidden lg:block absolute inset-x-0 bottom-0 overflow-hidden max-h-full whitespace-nowrap text-left text-xs font-normal bg-[#444] px-1.5">
          <span class="inline-block align-middle text-white text-xs font-light py-1">{!! $item->title !!}</span>
        </div>
        @endif

      </div>
    </li>
    @endforeach
    @endif

  </ul>

  @endif

</div>