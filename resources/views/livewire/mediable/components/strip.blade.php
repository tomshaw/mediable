<?php

use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;
use Livewire\Component;

new class extends Component {
    #[Reactive]
    public Collection $data;

    #[Reactive]
    public string $uniqueId;

    public array $selectedIds = [];

    #[On('attachments:selection-changed')]
    public function handleSelectionChanged(array $selectedIds, ?int $activeId): void
    {
        $this->selectedIds = $selectedIds;
    }

    public function setActiveAttachment(int $id): void
    {
        $this->dispatch('attachment:active-changed', id: $id);
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
    @if ($data->count())
    <ul class="flex items-center justify-start gap-x-2">
        @foreach($data as $item)
        <li class="shadow-md cursor-pointer" wire:click="setActiveAttachment({{$item->id}})">
            <div @class(['border border-black w-16 h-16 overflow-hidden', $this->isSelected($item->id) ? 'border-black' : 'border-black'])>
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
