<?php

namespace TomShaw\Mediable\Components\Panels;

use Livewire\Attributes\Reactive;
use Livewire\Component;

class StripPanel extends Component
{
    #[Reactive]
    public $data;

    #[Reactive]
    public array $selected = [];

    #[Reactive]
    public string $uniqueId = '';

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

    public function render()
    {
        return view('mediable::livewire.components.strip-panel');
    }
}
