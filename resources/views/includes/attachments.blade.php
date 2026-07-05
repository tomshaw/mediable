<div>
    @if ($this->paginator->isNotEmpty())
    <ul class="flex flex-wrap -mx-1 p-0 pl-1 w-full">
        @foreach($this->paginator as $item)
        <li @class([
            "attachment relative flex m-0 p-0 cursor-pointer list-none text-center select-none rounded-xl transition-colors duration-200",
            in_array($item->id, $selectedIds)
                ? "bg-indigo-50 dark:bg-indigo-500/10"
                : "hover:bg-zinc-200/50 dark:hover:bg-zinc-900"
        ]) id="attachment-id-{{ $item->id }}" wire:key="attachment-{{ $item->id }}" wire:click="toggleAttachment({{ $item->id }})" wire:island="selection" style="width: {{ $columnWidths[$defaultColumnWidth] }}%;">
            <div class="relative cursor-pointer flex items-center justify-center min-w-full" style="padding: {{ $this->normalizeColumnPadding($columnWidths[$defaultColumnWidth]) }}rem;">

                <div class="absolute left-2.5 top-1.5 cursor-pointer z-10">
                    <span @class([
                        "inline-flex items-center rounded-md px-1.5 py-0.5 font-mono text-[10px] tracking-wider",
                        in_array($item->id, $selectedIds)
                            ? "bg-indigo-600 text-white"
                            : "bg-white/70 text-zinc-500 ring-1 ring-zinc-950/5 dark:bg-zinc-800/70 dark:text-zinc-400 dark:ring-white/10 backdrop-blur-sm"
                    ])>{{ $item->id }}</span>
                </div>

                @if(in_array($item->id, $selectedIds))
                <div class="absolute right-2 top-1.5 z-10">
                    <span class="flex items-center justify-center w-5 h-5 rounded-full bg-indigo-600 text-white shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5">
                            <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                        </svg>
                    </span>
                </div>
                @endif

                @if (str_starts_with($item->file_type, 'image/'))
                <img src="{{ asset($item->file_url) }}?v={{ $this->cacheKey($item->updated_at) }}" @class([
                    'attachment__item object-contain rounded-lg shadow-sm',
                    in_array($item->id, $selectedIds)
                        ? 'ring-2 ring-indigo-500'
                        : 'ring-1 ring-zinc-950/10 dark:ring-white/10'
                ]) alt="{{ $item->file_original_name }}">
                @elseif (str_starts_with($item->file_type, 'video/'))
                <video src="{{ asset($item->file_url) }}" @class([
                    'attachment__item rounded-lg shadow-sm',
                    in_array($item->id, $selectedIds)
                        ? 'ring-2 ring-indigo-500'
                        : 'ring-1 ring-zinc-950/10 dark:ring-white/10'
                ]) alt="{{ $item->file_original_name }}" controls></video>
                @elseif (str_starts_with($item->file_type, 'audio/'))
                <div class="flex items-center justify-center h-full w-full overflow-hidden">

                    <audio class="hidden" id="audioPlayer{{ $item->id }}">
                        <source src="{{ asset($item->file_url) }}" type="{{ $item->file_type }}">
                    </audio>

                    <button wire:click.stop="playAudio({{ $item->id }})" wire:island="selection" id="playIcon{{ $item->id }}" @class(['w-12 h-12 bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900 rounded-full items-center justify-center shadow-sm cursor-pointer', ($item->id === $audioElementId) ? 'hidden' : 'flex'])>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 translate-x-px">
                            <path d="M6.3 2.84A1.5 1.5 0 0 0 4 4.11v11.78a1.5 1.5 0 0 0 2.3 1.27l9.344-5.891a1.5 1.5 0 0 0 0-2.538L6.3 2.841Z" />
                        </svg>
                    </button>

                    <button wire:click.stop="pauseAudio({{ $item->id }})" wire:island="selection" id="pauseIcon{{ $item->id }}" @class(['w-12 h-12 justify-between items-end p-2 box-border cursor-pointer gap-x-[1px]', ($item->id === $audioElementId) ? 'flex' : 'hidden'])>
                        <span class="audio-animation inline-block bg-indigo-500 rounded-sm w-1/3 h-[60%]" style="animation-delay: 0;"></span>
                        <span class="audio-animation inline-block bg-indigo-500 rounded-sm w-1/3 h-[30%]" style="animation-delay: -2.2s;"></span>
                        <span class="audio-animation inline-block bg-indigo-500 rounded-sm w-1/3 h-[75%]" style="animation-delay: -3.7s"></span>
                    </button>

                    <div class="absolute inset-x-0 bottom-0 overflow-hidden max-h-full whitespace-nowrap text-left text-xs font-normal rounded-b-xl">
                        <div class="absolute inset-y-0 left-0 h-full w-0 bg-indigo-600 z-0" id="audioProgress{{ $item->id }}"></div>
                        <span class="inline-block align-middle text-zinc-600 dark:text-zinc-300 font-mono text-[10px] py-1 px-1.5 relative z-10" id="audioText{{ $item->id }}"></span>
                    </div>

                </div>
                @else
                <div class="relative object-contain">
                    <div class="flex flex-col items-center justify-center gap-1.5 rounded-xl bg-white dark:bg-zinc-800 ring-1 ring-zinc-950/10 dark:ring-white/10 shadow-sm cursor-pointer w-24 h-24 p-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7 text-zinc-400 dark:text-zinc-500">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                        @if($item->file_original_name)
                        <span class="font-mono text-[10px] uppercase tracking-wider text-zinc-500 dark:text-zinc-400">{{ pathinfo($item->file_original_name, PATHINFO_EXTENSION) }}</span>
                        @endif
                    </div>
                </div>
                @endif

            </div>
        </li>
        @endforeach
    </ul>
    @endif
</div>
