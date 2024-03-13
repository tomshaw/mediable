<div class="flex flex-col justify-between p-0 m-0 w-full h-full">

    <div class="bg-gray-200 h-10 min-h-10 max-h-10 xl:h-11 xl:min-h-11 xl:max-h-11 2xl:h-12 2xl:min-h-12 2xl:max-h-12 w-full">
        <div class="flex items-center justify-between px-4 h-full w-full">
            <div></div>
            <div></div>
        </div>
    </div>

    <div class="flex-grow border-b border-t border-[#ccc] scrollY h-auto p-2">
        <div class="flex items-start justify-center p-0 m-0 w-full h-full">
            <div class="flex flex-col items-start justify-start w-full p-3 gap-y-1.5">

                @if ($this->mimeTypeTotals->total)
                <div class="flex items-center justify-between bg-[#555] hover:bg-[#444] select-none overflow-hidden rounded font-medium text-xs tracking-wider text-neutral-50 w-full py-1.5 px-2">
                    <span>FILES</span>
                    <span>{{ $this->mimeTypeTotals->total }} &ndash; {{ $this->formatBytes($this->mimeTypeTotals->total_size) }}</span>
                </div>
                @endif

                @foreach($this->mimeTypeStats as $item)
                <div class="flex items-center justify-between bg-[#555] hover:bg-[#444] select-none overflow-hidden rounded font-medium text-xs tracking-wider text-neutral-50 w-full py-1.5 px-2">
                    <span>{{ strtoupper(collect(explode('/', $item->file_type))->last()) }}</span>
                    <span>{{ $item->total }} &ndash; {{ $this->formatBytes($item->total_size) }}</span>
                </div>
                @endforeach

            </div>
        </div>
    </div>

    <div class="bg-gray-200 h-10 min-h-10 max-h-10 xl:h-11 xl:min-h-11 xl:max-h-11 2xl:h-12 2xl:min-h-12 2xl:max-h-12 w-full">
        <div class="flex items-center justify-start px-4 h-full w-full gap-x-2"></div>
    </div>
</div>