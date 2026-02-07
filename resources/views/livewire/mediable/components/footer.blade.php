<?php

use Illuminate\Support\Collection;
use Livewire\Attributes\{Computed, On, Reactive};
use Livewire\Component;
use TomShaw\Mediable\Models\Attachment;

new class extends Component
{
    #[Reactive]
    public string $uniqueId;

    public array $selectedIds = [];

    public ?int $activeId = null;

    #[On('attachments:selection-changed')]
    public function handleSelectionChanged(array $selectedIds, ?int $activeId): void
    {
        $this->selectedIds = $selectedIds;
        $this->activeId = $activeId;
    }

    #[Computed]
    public function selected(): Collection
    {
        if (empty($this->selectedIds)) {
            return collect();
        }

        return Attachment::whereIn('id', $this->selectedIds)->get();
    }

    public function setActiveAttachment(int $id): void
    {
        if ($this->activeId === $id) {
            $this->activeId = null;
            $this->dispatch('attachment:active-cleared');
        } else {
            $this->activeId = $id;
            $this->dispatch('attachment:active-changed', id: $id);
        }
    }

    public function confirmDelete(): void
    {
        $this->dispatch('mediable.confirm',
            message: 'Are you sure you want to delete the selected attachments?',
            type: 'delete.selected',
            selectedIds: $this->selectedIds,
        );
    }

    public function clearSelected(): void
    {
        $this->dispatch('attachments:clear-selected');
    }

    public function insertMedia(): void
    {
        $this->dispatch('panel:insert-media', selectedIds: $this->selectedIds);
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
}; ?>

<div class="flex items-center justify-between h-full py-0 px-4">
    <div class="flex items-center justify-start gap-2">
        @if (count($selectedIds))
        <button wire:click="confirmDelete" class="group relative inline-flex items-center justify-center overflow-hidden rounded-md bg-[#555] py-1.5 px-2 font-medium text-xs tracking-wider text-neutral-50 cursor-pointer">
            <span class="absolute h-0 w-0 rounded-full bg-red-500 transition-all duration-300 group-hover:h-56 group-hover:w-32"></span>
            <span class="relative">[#]</span>
        </button>
        <button wire:click="clearSelected" class="group relative inline-flex items-center justify-center overflow-hidden rounded-md bg-[#555] py-1.5 px-2 font-medium text-xs tracking-wider text-neutral-50 cursor-pointer">
            <span class="absolute h-0 w-0 rounded-full bg-blue-500 transition-all duration-300 group-hover:h-56 group-hover:w-32"></span>
            <span class="relative">Clear Selected</span>
        </button>
        @endif
        <ul class="flex items-center justify-start ml-4 gap-x-2">
            @foreach($this->selected as $item)
            <li class="shadow-md cursor-pointer" wire:click="setActiveAttachment({{ $item->id }})">
                <div @class(['border border-black overflow-hidden', ($activeId && $item->id === $activeId) ? 'w-11 h-11' : 'w-9 h-9'])>
                    @if ($this->mimeTypeImage($item->file_type))
                    <img src="{{ $item->file_url }}?id={{ $uniqueId }}" class="w-full h-full object-cover" alt="{{ $item->title }}" />
                    @elseif ($this->mimeTypeVideo($item->file_type))
                    <img src="{{ asset("vendor/mediable/images/video.png") }}" class="w-full h-full object-cover" alt="{{ $item->title }}" />
                    @elseif ($this->mimeTypeAudio($item->file_type))
                    <img src="{{ asset("vendor/mediable/images/audio.png") }}" class="w-full h-full object-cover" alt="{{ $item->title }}" />
                    @else
                    <img src="{{ asset("vendor/mediable/images/file.png") }}" class="w-full h-full object-cover" alt="{{ $item->title }}" />
                    @endif
                </div>
            </li>
            @endforeach
        </ul>
    </div>
    <div class="flex items-center justify-start gap-2">
        @if (count($selectedIds))
        <button wire:click="insertMedia" class="group relative inline-flex items-center justify-center overflow-hidden rounded-md bg-[#555] py-1.5 px-3 font-medium text-xs tracking-wider text-neutral-50 cursor-pointer">
            <span class="absolute h-0 w-0 rounded-full bg-blue-500 transition-all duration-300 group-hover:h-56 group-hover:w-32"></span>
            <span class="relative">Attach Selected</span>
        </button>
        @endif
    </div>
</div>
