<div>
    @if ($this->paginator->isNotEmpty())
    <ul class="flex items-center justify-start gap-x-2 py-2">
        @foreach($this->paginator as $item)
        <li class="cursor-pointer" wire:key="strip-{{ $item->id }}" wire:click="setActiveAttachment({{ $item->id }})">
            <div @class([
                'overflow-hidden rounded-md bg-white dark:bg-zinc-800 transition-all duration-200',
                ($activeId && $item->id === $activeId)
                    ? 'w-18 h-18 ring-2 ring-indigo-500 shadow-md'
                    : 'w-14 h-14 ring-1 ring-zinc-950/10 dark:ring-white/10 hover:ring-zinc-400 dark:hover:ring-zinc-500 shadow-sm'
            ])>
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
