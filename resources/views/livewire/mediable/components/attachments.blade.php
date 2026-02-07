<?php

use Livewire\Attributes\{On, Reactive};
use Livewire\Component;

new class extends Component
{
    #[Reactive]
    public array $attachments = [];

    #[Reactive]
    public string $uniqueId;

    #[Reactive]
    public array $columnWidths;

    #[Reactive]
    public int $defaultColumnWidth;

    public array $selectedIds = [];

    public function mount(array $attachments, string $uniqueId, array $columnWidths, int $defaultColumnWidth): void
    {
        $this->attachments = $attachments;
        $this->uniqueId = $uniqueId;
        $this->columnWidths = $columnWidths;
        $this->defaultColumnWidth = $defaultColumnWidth;
    }

    public ?int $audioElementId = null;

    public function playAudio(int $id): void
    {
        $this->audioElementId = $id;
        $this->dispatch('audio.start', id: $id);
    }

    public function pauseAudio(int $id): void
    {
        if ($this->audioElementId === $id) {
            $this->audioElementId = null;
        }
        $this->dispatch('audio.pause', id: $id);
    }

    #[On('attachments:reset-audio')]
    public function resetAudio(): void
    {
        $this->audioElementId = null;
    }

    public function toggleAttachment(int $id): void
    {
        if (in_array($id, $this->selectedIds)) {
            $this->selectedIds = array_values(array_diff($this->selectedIds, [$id]));
        } else {
            $this->selectedIds[] = $id;
        }

        $this->dispatch('attachments:selection-changed',
            selectedIds: $this->selectedIds,
            activeId: $id
        );

        $this->dispatch('mediable.scroll', id: $id);
    }

    #[On('attachments:clear-selected')]
    public function clearSelected(): void
    {
        $this->selectedIds = [];

        $this->dispatch('attachments:selection-changed',
            selectedIds: $this->selectedIds,
            activeId: null
        );
    }

    #[On('attachments:remove-item')]
    public function removeItem(int $id): void
    {
        $this->selectedIds = array_values(array_diff($this->selectedIds, [$id]));

        $activeId = count($this->selectedIds) ? end($this->selectedIds) : null;

        $this->dispatch('attachments:selection-changed',
            selectedIds: $this->selectedIds,
            activeId: $activeId
        );
    }

    public function isSelected(int $id): bool
    {
        return in_array($id, $this->selectedIds);
    }

    public function mimeTypeImage(string $mimeType): bool
    {
        return str_starts_with($mimeType, 'image/');
    }

    public function mimeTypeVideo(string $mimeType): bool
    {
        return str_starts_with($mimeType, 'video/');
    }

    public function mimeTypeAudio(string $mimeType): bool
    {
        return str_starts_with($mimeType, 'audio/');
    }

    public function normalizeColumnPadding(float $width): float
    {
        return match (true) {
            $width <= 10 => 2.5,
            $width <= 15 => 3,
            $width <= 20 => 3.5,
            $width <= 25 => 4,
            default => 4.5,
        };
    }
}; ?>

<div>
    @if (count($attachments))
    <ul class="flex flex-wrap -mx-1 p-0 pl-1 w-full">
        @foreach($attachments as $item)
        <li @class([
            "attachment relative flex m-0 p-0 cursor-pointer list-none text-center select-none border-b border-r border-gray-300",
            $this->isSelected($item['id']) ? "bg-gray-200" : "bg-gray-200"
        ]) id="attachment-id-{{$item['id']}}" wire:click="toggleAttachment({{ $item['id'] }})" style="width: {{$columnWidths[$defaultColumnWidth]}}%;">
            <div class="relative cursor-pointer flex items-center justify-center min-w-full" style="padding: {{ $this->normalizeColumnPadding($columnWidths[$defaultColumnWidth]) }}rem;">

                <div class="absolute left-2.5 top-1 cursor-pointer bg-transparent">
                    <span class="text-left bg-transparent font-medium text-xs tracking-wider text-neutral-600">{{$item['id']}}</span>
                </div>

                @if($this->isSelected($item['id']))
                <div class="absolute right-2.5 top-2 cursor-pointer bg-transparent text-neutral-600">
                    <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1 9.50006C1 10.3285 1.67157 11.0001 2.5 11.0001H4L4 10.0001H2.5C2.22386 10.0001 2 9.7762 2 9.50006L2 2.50006C2 2.22392 2.22386 2.00006 2.5 2.00006L9.5 2.00006C9.77614 2.00006 10 2.22392 10 2.50006V4.00002H5.5C4.67158 4.00002 4 4.67159 4 5.50002V12.5C4 13.3284 4.67158 14 5.5 14H12.5C13.3284 14 14 13.3284 14 12.5V5.50002C14 4.67159 13.3284 4.00002 12.5 4.00002H11V2.50006C11 1.67163 10.3284 1.00006 9.5 1.00006H2.5C1.67157 1.00006 1 1.67163 1 2.50006V9.50006ZM5 5.50002C5 5.22388 5.22386 5.00002 5.5 5.00002H12.5C12.7761 5.00002 13 5.22388 13 5.50002V12.5C13 12.7762 12.7761 13 12.5 13H5.5C5.22386 13 5 12.7762 5 12.5V5.50002Z" fill="currentColor" fill-rule="evenodd" clip-rule="evenodd"></path>
                    </svg>
                </div>
                @endif

                @if ($this->mimeTypeImage($item['file_type']))
                <img src="{{ asset($item['file_url']) }}?id={{ $uniqueId }}" class="attachment__item object-contain shadow-md border border-black" alt="{{ $item['file_original_name'] }}">
                @elseif ($this->mimeTypeVideo($item['file_type']))
                <video src="{{ asset($item['file_url']) }}" class="attachment__item" alt="{{ $item['file_original_name'] }}" controls></video>
                @elseif ($this->mimeTypeAudio($item['file_type']))
                <div class="flex items-center justify-center h-full w-full overflow-hidden">

                    <audio class="hidden" id="audioPlayer{{ $item['id'] }}">
                        <source src="{{ asset($item['file_url']) }}" type="{{ $item['file_type'] }}">
                    </audio>

                    <button wire:click.stop="playAudio({{ $item['id'] }})" id="playIcon{{ $item['id'] }}" @class(['w-12 h-12 bg-[#555] rounded-full items-center justify-center cursor-pointer', ($item['id'] === $audioElementId) ? 'hidden' : 'flex'])>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6 text-white">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>

                    <button wire:click.stop="pauseAudio({{ $item['id'] }})" id="pauseIcon{{ $item['id'] }}" @class(['bg-white bg-opacity-10 w-12 h-12 justify-between items-end p-2 box-border cursor-pointer gap-x-[1px]', ($item['id'] === $audioElementId) ? 'flex' : 'hidden'])>
                        <span class="audio-animation inline-block bg-[#555] w-1/3 h-[60%]" style="animation-delay: 0;"></span>
                        <span class="audio-animation inline-block bg-[#555] w-1/3 h-[30%]" style="animation-delay: -2.2s;"></span>
                        <span class="audio-animation inline-block bg-[#555] w-1/3 h-[75%]" style="animation-delay: -3.7s"></span>
                    </button>

                    <div class="absolute inset-x-0 bottom-0 overflow-hidden max-h-full whitespace-nowrap text-left text-xs font-normal px-1.5">
                        <div class="absolute inset-y-0 left-0 h-full w-0 bg-blue-500 z-0" id="audioProgress{{$item['id']}}"></div>
                        <span class="inline-block align-middle text-white text-xs font-light py-1 relative z-10" id="audioText{{$item['id']}}"></span>
                    </div>

                </div>
                @else
                <div class="relative object-contain">
                    <div class="flex items-center justify-center text-white bg-[#555] rounded-full cursor-pointer w-12 h-12">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6 ">
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
</div>
