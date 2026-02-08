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

    public array $selectedIds = [];

    public ?int $activeId = null;

    #[On(BrowserEvents::ATTACHMENTS_SELECTION_CHANGED->value)]
    public function handleSelectionChanged(array $selectedIds, ?int $activeId): void
    {
        $this->selectedIds = $selectedIds;
        $this->activeId = $activeId;
    }

    public function setActiveAttachment(int $id): void
    {
        if ($this->activeId === $id) {
            $this->activeId = null;
            $this->dispatch(BrowserEvents::ATTACHMENT_ACTIVE_CLEARED->value);
        } else {
            $this->activeId = $id;
            $this->dispatch(BrowserEvents::ATTACHMENT_ACTIVE_CHANGED->value, id: $id);
        }
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
}; ?>

<div>
    @if (count($attachments))
    <ul class="flex items-center justify-start gap-x-2">
        @foreach($attachments as $item)
        <li class="shadow-md cursor-pointer" wire:click="setActiveAttachment({{$item['id']}})">
            <div @class(['border overflow-hidden transition-all duration-200', ($activeId && $item['id'] === $activeId) ? 'w-20 h-20 border-blue-500 ring-2 ring-blue-500/40' : 'w-16 h-16 border-black'])>
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
</div>
