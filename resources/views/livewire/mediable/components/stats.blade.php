<?php

use Livewire\Component;
use TomShaw\Mediable\Traits\{WithFileSize, WithReporting};

new class extends Component
{
    use WithFileSize;
    use WithReporting;
}; ?>

<div class="flex flex-col justify-between p-0 m-0 w-full h-full">

    <div class="h-12 min-h-12 max-h-12 w-full">
        <div class="flex items-center justify-between px-4 h-full w-full">
            <span class="text-[11px] font-medium uppercase tracking-widest text-zinc-400 dark:text-zinc-500 select-none">Library stats</span>
            <div></div>
        </div>
    </div>

    <div class="grow border-b border-t border-zinc-200 dark:border-zinc-800 scrollY h-auto p-2">
        <div class="flex items-start justify-center p-0 m-0 w-full h-full">
            <div class="flex flex-col items-start justify-start w-full p-2 gap-y-1">

                @if ($this->mimeTypeTotals->total)
                <div class="flex items-center justify-between select-none rounded-md w-full py-1 px-1.5 mb-1 border-b border-zinc-200 dark:border-zinc-800">
                    <span class="text-[10px] font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">All files</span>
                    <span class="font-mono text-xs text-zinc-700 dark:text-zinc-200 tabular-nums">{{ $this->mimeTypeTotals->total }} &middot; {{ $this->formatBytes($this->mimeTypeTotals->total_size) }}</span>
                </div>
                @endif

                @foreach($this->mimeTypeStats as $item)
                <div class="flex items-center justify-between select-none rounded-md w-full py-1 px-1.5 hover:bg-zinc-200/60 dark:hover:bg-zinc-800 transition-colors">
                    <span class="text-[10px] font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ strtoupper(collect(explode('/', $item->file_type))->last()) }}</span>
                    <span class="font-mono text-xs text-zinc-600 dark:text-zinc-300 tabular-nums">{{ $item->total }} &middot; {{ $this->formatBytes($item->total_size) }}</span>
                </div>
                @endforeach

            </div>
        </div>
    </div>

    <div class="h-12 min-h-12 max-h-12 w-full">
        <div class="flex items-center justify-start px-4 h-full w-full gap-x-2"></div>
    </div>
</div>
