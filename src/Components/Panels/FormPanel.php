<?php

namespace TomShaw\Mediable\Components\Panels;

use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;
use Livewire\Component;
use TomShaw\Mediable\Concerns\AttachmentState;
use TomShaw\Mediable\Traits\{WithFonts, WithGraphicDraw};

class FormPanel extends Component
{
    use WithFonts;
    use WithGraphicDraw;

    #[Reactive]
    public ?AttachmentState $attachment = null;

    public string $uniqueId = '';

    public function mount(string $uniqueId = ''): void
    {
        $this->uniqueId = $uniqueId;
    }

    #[On('panel:regenerate-unique-id')]
    public function handleRegenerateUniqueId(string $uniqueId): void
    {
        $this->uniqueId = $uniqueId;
    }

    public function generateUniqueId(): void
    {
        $this->uniqueId = uniqid();
        $this->dispatch('panel:unique-id-updated', uniqueId: $this->uniqueId);
    }

    public function saveEditorChanges(): void
    {
        $this->dispatch('panel:save-editor-changes');
    }

    public function undoEditorChanges(): void
    {
        $this->dispatch('panel:undo-editor-changes');
    }

    public function mimeTypeImage(string $mimeType): bool
    {
        return str_starts_with($mimeType, 'image/');
    }

    public function render()
    {
        return view('mediable::livewire.components.form-panel');
    }
}
