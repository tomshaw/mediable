<?php

namespace TomShaw\Mediable\Components\Panels;

use Livewire\Attributes\Reactive;
use Livewire\Component;
use TomShaw\Mediable\Concerns\AttachmentState;

class SidebarPanel extends Component
{
    #[Reactive]
    public ?AttachmentState $attachment = null;

    public string $title = '';

    public string $caption = '';

    public int $sort_order = 0;

    public string $styles = '';

    public string $description = '';

    public function mount(): void
    {
        $this->syncFormFromAttachment();
    }

    public function updatedAttachment(): void
    {
        $this->syncFormFromAttachment();
    }

    protected function syncFormFromAttachment(): void
    {
        if ($this->attachment) {
            $this->title = $this->attachment->title ?? '';
            $this->caption = $this->attachment->caption ?? '';
            $this->sort_order = $this->attachment->sort_order ?? 0;
            $this->styles = $this->attachment->styles ?? '';
            $this->description = $this->attachment->description ?? '';
        }
    }

    public function updateAttachment(): void
    {
        $this->dispatch('panel:update-attachment', data: [
            'title' => $this->title,
            'caption' => $this->caption,
            'sort_order' => $this->sort_order,
            'styles' => $this->styles,
            'description' => $this->description,
        ]);
    }

    public function render()
    {
        return view('mediable::livewire.components.sidebar-panel');
    }
}
