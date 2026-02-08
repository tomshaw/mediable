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

<div class="flex items-center justify-between h-full px-4 gap-4">

    {{-- Left: count + thumbnails --}}
    <div class="flex items-center gap-3 min-w-0">
        @if (count($selectedIds))
        <span class="shrink-0 inline-flex items-center gap-1.5 rounded-full bg-neutral-900 px-2.5 py-0.5 text-xs font-semibold text-gray-50 tabular-nums">
            {{ count($selectedIds) }}
            <span class="hidden sm:inline font-normal">selected</span>
        </span>

        <div class="shrink-0 w-px h-6 bg-gray-400/60"></div>

        <div class="flex items-center gap-2 min-w-0">
            @foreach($this->selected as $item)
            <div wire:click="setActiveAttachment({{ $item->id }})" @class([
                'shrink-0 cursor-pointer transition-all duration-200 border bg-white p-px',
                ($activeId && $item->id === $activeId)
                    ? 'w-11 h-11 border-red-600'
                    : 'w-9 h-9 border-black'
            ])>
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
            @endforeach
        </div>
        @else
        <span class="text-xs text-gray-500 italic select-none">Click attachments to select them</span>
        @endif
    </div>

    {{-- Right: actions --}}
    @if (count($selectedIds))
    <div class="flex items-center shrink-0 gap-2">
        <button wire:click="clearSelected" class="group relative inline-flex items-center justify-center overflow-hidden rounded-md bg-neutral-900 py-1.5 px-3 font-medium text-xs tracking-wider text-gray-50 cursor-pointer" title="Clear selection">
            <span class="absolute h-0 w-0 rounded-full bg-blue-500 transition-all duration-300 group-hover:h-56 group-hover:w-32"></span>
            <span class="relative inline-flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-3.5 h-3.5">
                    <path d="M5.28 4.22a.75.75 0 0 0-1.06 1.06L6.94 8l-2.72 2.72a.75.75 0 1 0 1.06 1.06L8 9.06l2.72 2.72a.75.75 0 1 0 1.06-1.06L9.06 8l2.72-2.72a.75.75 0 0 0-1.06-1.06L8 6.94 5.28 4.22Z" />
                </svg>
                Clear
            </span>
        </button>

        <button wire:click="confirmDelete" class="group relative inline-flex items-center justify-center overflow-hidden rounded-md bg-neutral-900 py-1.5 px-3 font-medium text-xs tracking-wider text-gray-50 cursor-pointer" title="Delete selected attachments">
            <span class="absolute h-0 w-0 rounded-full bg-red-500 transition-all duration-300 group-hover:h-56 group-hover:w-32"></span>
            <span class="relative inline-flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-3.5 h-3.5">
                    <path fill-rule="evenodd" d="M5 3.25V4H2.75a.75.75 0 0 0 0 1.5h.3l.815 8.15A1.5 1.5 0 0 0 5.357 15h5.285a1.5 1.5 0 0 0 1.493-1.35l.815-8.15h.3a.75.75 0 0 0 0-1.5H11v-.75A2.25 2.25 0 0 0 8.75 1h-1.5A2.25 2.25 0 0 0 5 3.25Zm2.25-.75a.75.75 0 0 0-.75.75V4h3v-.75a.75.75 0 0 0-.75-.75h-1.5ZM6.05 6a.75.75 0 0 1 .787.713l.275 5.5a.75.75 0 0 1-1.498.075l-.275-5.5A.75.75 0 0 1 6.05 6Zm3.9 0a.75.75 0 0 1 .712.787l-.275 5.5a.75.75 0 0 1-1.498-.075l.275-5.5A.75.75 0 0 1 9.95 6Z" clip-rule="evenodd" />
                </svg>
                Delete
            </span>
        </button>

        <div class="shrink-0 w-px h-6 bg-gray-400/60"></div>

        <button wire:click="insertMedia" class="group relative inline-flex items-center justify-center overflow-hidden rounded-md bg-neutral-900 py-1.5 px-3 font-medium text-xs tracking-wider text-gray-50 cursor-pointer">
            <span class="absolute h-0 w-0 rounded-full bg-blue-500 transition-all duration-300 group-hover:h-56 group-hover:w-32"></span>
            <span class="relative inline-flex items-center gap-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-3.5 h-3.5">
                    <path fill-rule="evenodd" d="M8 2a.75.75 0 0 1 .75.75V7h4.25a.75.75 0 0 1 0 1.5H8.75v4.25a.75.75 0 0 1-1.5 0V8.5H3a.75.75 0 0 1 0-1.5h4.25V2.75A.75.75 0 0 1 8 2Z" clip-rule="evenodd" />
                </svg>
                Attach Selected
            </span>
        </button>
    </div>
    @endif

</div>
