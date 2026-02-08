<?php

use Livewire\Attributes\{On, Reactive};
use Livewire\Component;
use TomShaw\Mediable\Enums\BrowserEvents;

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
        $this->dispatch(BrowserEvents::AUDIO_START->value, id: $id);
    }

    public function pauseAudio(int $id): void
    {
        if ($this->audioElementId === $id) {
            $this->audioElementId = null;
        }
        $this->dispatch(BrowserEvents::AUDIO_PAUSE->value, id: $id);
    }

    #[On(BrowserEvents::ATTACHMENTS_RESET_AUDIO->value)]
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

        $this->dispatch(BrowserEvents::ATTACHMENTS_SELECTION_CHANGED->value,
            selectedIds: $this->selectedIds,
            activeId: $id
        );

        $this->dispatch(BrowserEvents::SCROLL->value, id: $id);
    }

    #[On(BrowserEvents::ATTACHMENTS_TOGGLE_ITEM->value)]
    public function handleToggleItem(int $id): void
    {
        $this->toggleAttachment($id);
    }

    #[On(BrowserEvents::ATTACHMENTS_CLEAR_SELECTED->value)]
    public function clearSelected(): void
    {
        $this->selectedIds = [];

        $this->dispatch(BrowserEvents::ATTACHMENTS_SELECTION_CHANGED->value,
            selectedIds: $this->selectedIds,
            activeId: null
        );
    }

    #[On(BrowserEvents::ATTACHMENTS_REMOVE_ITEM->value)]
    public function removeItem(int $id): void
    {
        $this->selectedIds = array_values(array_diff($this->selectedIds, [$id]));

        $activeId = count($this->selectedIds) ? end($this->selectedIds) : null;

        $this->dispatch(BrowserEvents::ATTACHMENTS_SELECTION_CHANGED->value,
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
            "attachment relative flex m-0 p-0 cursor-pointer list-none text-center select-none border-b border-r transition-all duration-200",
            $this->isSelected($item['id'])
                ? "bg-gray-300 border-blue-400 ring-2 ring-inset ring-blue-500/40"
                : "bg-gray-200 border-gray-300 hover:bg-gray-100"
        ]) id="attachment-id-{{$item['id']}}" wire:click="toggleAttachment({{ $item['id'] }})" style="width: {{$columnWidths[$defaultColumnWidth]}}%;">
            <div class="relative cursor-pointer flex items-center justify-center min-w-full" style="padding: {{ $this->normalizeColumnPadding($columnWidths[$defaultColumnWidth]) }}rem;">

                <div class="absolute left-2.5 top-1 cursor-pointer bg-transparent">
                    <span @class([
                        "text-left bg-transparent font-medium text-xs tracking-wider",
                        $this->isSelected($item['id']) ? "text-blue-700" : "text-neutral-600"
                    ])>{{$item['id']}}</span>
                </div>

                @if($this->isSelected($item['id']))
                <div class="absolute right-2 top-1.5 z-10">
                    <span class="flex items-center justify-center w-5 h-5 rounded-full bg-blue-600 text-white shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5">
                            <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                        </svg>
                    </span>
                </div>
                @endif

                @if ($this->mimeTypeImage($item['file_type']))
                <img src="{{ asset($item['file_url']) }}?id={{ $uniqueId }}" @class([
                    'attachment__item object-contain shadow-md border',
                    $this->isSelected($item['id']) ? 'border-blue-500' : 'border-black'
                ]) alt="{{ $item['file_original_name'] }}">
                @elseif ($this->mimeTypeVideo($item['file_type']))
                <video src="{{ asset($item['file_url']) }}" class="attachment__item" alt="{{ $item['file_original_name'] }}" controls></video>
                @elseif ($this->mimeTypeAudio($item['file_type']))
                <div class="flex items-center justify-center h-full w-full overflow-hidden">

                    <audio class="hidden" id="audioPlayer{{ $item['id'] }}">
                        <source src="{{ asset($item['file_url']) }}" type="{{ $item['file_type'] }}">
                    </audio>

                    <button wire:click.stop="playAudio({{ $item['id'] }})" id="playIcon{{ $item['id'] }}" @class(['w-12 h-12 bg-neutral-900 rounded-full items-center justify-center cursor-pointer', ($item['id'] === $audioElementId) ? 'hidden' : 'flex'])>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6 text-white">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>

                    <button wire:click.stop="pauseAudio({{ $item['id'] }})" id="pauseIcon{{ $item['id'] }}" @class(['bg-white bg-opacity-10 w-12 h-12 justify-between items-end p-2 box-border cursor-pointer gap-x-[1px]', ($item['id'] === $audioElementId) ? 'flex' : 'hidden'])>
                        <span class="audio-animation inline-block bg-neutral-900 w-1/3 h-[60%]" style="animation-delay: 0;"></span>
                        <span class="audio-animation inline-block bg-neutral-900 w-1/3 h-[30%]" style="animation-delay: -2.2s;"></span>
                        <span class="audio-animation inline-block bg-neutral-900 w-1/3 h-[75%]" style="animation-delay: -3.7s"></span>
                    </button>

                    <div class="absolute inset-x-0 bottom-0 overflow-hidden max-h-full whitespace-nowrap text-left text-xs font-normal px-1.5">
                        <div class="absolute inset-y-0 left-0 h-full w-0 bg-blue-500 z-0" id="audioProgress{{$item['id']}}"></div>
                        <span class="inline-block align-middle text-white text-xs font-light py-1 relative z-10" id="audioText{{$item['id']}}"></span>
                    </div>

                </div>
                @else
                <div class="relative object-contain">
                    <div class="flex items-center justify-center text-white bg-neutral-900 rounded-full cursor-pointer w-12 h-12">
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
