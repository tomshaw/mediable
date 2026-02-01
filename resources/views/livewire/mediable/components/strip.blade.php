<?php

use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Reactive;
use Livewire\Component;

new class extends Component {
    #[Reactive]
    public Collection $data;

    #[Reactive]
    public array $selected;

    #[Reactive]
    public string $uniqueId;

    public function toggleAttachment(int $id): void
    {
        $this->dispatch('panel:toggle-attachment', id: $id);
    }

    public function isSelected(int $id): bool
    {
        return in_array($id, array_column($this->selected, 'id'));
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
        <li class="shadow-md cursor-pointer" wire:click="toggleAttachment({{$item->id}})">
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
