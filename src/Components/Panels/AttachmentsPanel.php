<?php

namespace TomShaw\Mediable\Components\Panels;

use Livewire\Attributes\Reactive;
use Livewire\Component;

class AttachmentsPanel extends Component
{
    #[Reactive]
    public $data;

    #[Reactive]
    public array $selected = [];

    #[Reactive]
    public ?int $audioElementId = null;

    #[Reactive]
    public string $uniqueId = '';

    #[Reactive]
    public array $columnWidths = [];

    #[Reactive]
    public string $defaultColumnWidth = '';

    public function toggleAttachment(int $id): void
    {
        $this->dispatch('panel:toggle-attachment', id: $id);
    }

    public function playAudio(int $id): void
    {
        $this->dispatch('audio.start', id: $id);
    }

    public function pauseAudio(int $id): void
    {
        $this->dispatch('audio.pause', id: $id);
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

    public function render()
    {
        return view('mediable::livewire.components.attachments-panel');
    }
}
