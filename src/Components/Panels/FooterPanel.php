<?php

namespace TomShaw\Mediable\Components\Panels;

use Livewire\Attributes\Reactive;
use Livewire\Component;
use TomShaw\Mediable\Concerns\AttachmentState;

class FooterPanel extends Component
{
    #[Reactive]
    public array $selected = [];

    #[Reactive]
    public ?AttachmentState $attachment = null;

    #[Reactive]
    public string $uniqueId = '';

    public function confirmDelete(): void
    {
        $this->dispatch('panel:confirm-delete');
    }

    public function clearSelected(): void
    {
        $this->dispatch('panel:clear-selected');
    }

    public function setActiveAttachment(array $item): void
    {
        $this->dispatch('panel:set-active-attachment', item: $item);
    }

    public function insertMedia(): void
    {
        $this->dispatch('panel:insert-media');
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
        return view('mediable::livewire.components.footer-panel');
    }
}
