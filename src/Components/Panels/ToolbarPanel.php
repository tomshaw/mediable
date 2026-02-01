<?php

namespace TomShaw\Mediable\Components\Panels;

use Livewire\Attributes\Reactive;
use Livewire\Component;
use TomShaw\Mediable\Concerns\{AttachmentState, PanelState, ShowState};

class ToolbarPanel extends Component
{
    #[Reactive]
    public PanelState $panel;

    #[Reactive]
    public ShowState $show;

    #[Reactive]
    public $data;

    #[Reactive]
    public array $files = [];

    #[Reactive]
    public array $selected = [];

    #[Reactive]
    public ?AttachmentState $attachment = null;

    #[Reactive]
    public array $orderColumns = [];

    #[Reactive]
    public array $columnWidths = [];

    #[Reactive]
    public array $uniqueMimeTypes = [];

    public string $orderBy = 'id';

    public string $orderDir = 'DESC';

    public string $defaultColumnWidth = '';

    public string $selectedMimeType = '';

    public function mount(
        string $orderBy = 'id',
        string $orderDir = 'DESC',
        string $defaultColumnWidth = '',
        string $selectedMimeType = ''
    ): void {
        $this->orderBy = $orderBy;
        $this->orderDir = $orderDir;
        $this->defaultColumnWidth = $defaultColumnWidth;
        $this->selectedMimeType = $selectedMimeType;
    }

    public function updatedOrderBy(): void
    {
        $this->dispatch('toolbar:order-by-changed', orderBy: $this->orderBy);
    }

    public function updatedDefaultColumnWidth(): void
    {
        $this->dispatch('toolbar:column-width-changed', defaultColumnWidth: $this->defaultColumnWidth);
    }

    public function updatedSelectedMimeType(): void
    {
        $this->dispatch('toolbar:mime-type-changed', selectedMimeType: $this->selectedMimeType);
    }

    public function enableThumbMode(): void
    {
        $this->dispatch('toolbar:enable-thumb-mode');
    }

    public function enableUploadMode(): void
    {
        $this->dispatch('toolbar:enable-upload-mode');
    }

    public function enableEditorMode(): void
    {
        $this->dispatch('toolbar:enable-editor-mode');
    }

    public function clearFiles(): void
    {
        $this->dispatch('toolbar:clear-files');
    }

    public function createAttachments(): void
    {
        $this->dispatch('toolbar:create-attachments');
    }

    public function toggleMetaInfo(): void
    {
        $this->dispatch('toolbar:toggle-meta-info');
    }

    public function toggleOrderDir(): void
    {
        $this->orderDir = strtoupper($this->orderDir) === 'ASC' ? 'DESC' : 'ASC';
        $this->dispatch('toolbar:order-dir-changed', orderDir: $this->orderDir);
    }

    public function toggleSidebar(): void
    {
        $this->dispatch('toolbar:toggle-sidebar');
    }

    public function deleteAttachment(int $id): void
    {
        $this->dispatch('toolbar:delete-attachment', id: $id);
    }

    public function closeImageEditor(): void
    {
        $this->dispatch('toolbar:close-image-editor');
    }

    public function render()
    {
        return view('mediable::livewire.components.toolbar-panel');
    }
}
