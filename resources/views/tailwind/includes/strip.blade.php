@if ($data->count())
<ul class="flex items-center justify-start gap-x-2">
    @foreach($data as $item)
    <li class="shadow-md cursor-pointer" wire:click="toggleAttachment({{$item->id}})">
        <div @class(['border border-black w-16 h-16 overflow-hidden', in_array($item->id, array_column($this->selected, 'id')) ? 'border-black' : 'border-black'])>
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
@endif