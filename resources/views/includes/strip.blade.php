<div>
    @if ($this->paginator->isNotEmpty())
    <ul class="flex items-center justify-start gap-x-2">
        @foreach($this->paginator as $item)
        <li class="shadow-md cursor-pointer" wire:key="strip-{{ $item->id }}" wire:click="setActiveAttachment({{ $item->id }})">
            <div @class(['border overflow-hidden transition-all duration-200', ($activeId && $item->id === $activeId) ? 'w-20 h-20 border-blue-500 ring-2 ring-blue-500/40' : 'w-16 h-16 border-black'])>
                @if (str_starts_with($item->file_type, 'image/'))
                <img src="{{ $item->file_url }}?v={{ $this->cacheKey($item->updated_at) }}" class="w-full h-full object-cover" alt="{{ $item->title }}" />
                @elseif (str_starts_with($item->file_type, 'video/'))
                <img src="{{ asset("vendor/mediable/images/video.png") }}" class="w-full h-full object-cover" alt="{{ $item->title }}" />
                @elseif (str_starts_with($item->file_type, 'audio/'))
                <img src="{{ asset("vendor/mediable/images/audio.png") }}" class="w-full h-full object-cover" alt="{{ $item->title }}" />
                @else
                <img src="{{ asset("vendor/mediable/images/file.png") }}" class="w-full h-full object-cover" alt="{{ $item->title }}" />
                @endif
            </div>
        </li>
        @endforeach
    </ul>
    @endif
</div>
