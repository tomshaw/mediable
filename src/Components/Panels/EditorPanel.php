<?php

namespace TomShaw\Mediable\Components\Panels;

use Livewire\Attributes\Reactive;
use Livewire\Component;
use TomShaw\Mediable\Concerns\AttachmentState;

class EditorPanel extends Component
{
    #[Reactive]
    public ?AttachmentState $attachment = null;

    #[Reactive]
    public string $uniqueId = '';

    public function mimeTypeImage(string $mimeType): bool
    {
        return str_starts_with($mimeType, 'image/');
    }

    public function render()
    {
        return view('mediable::livewire.components.editor-panel');
    }
}
