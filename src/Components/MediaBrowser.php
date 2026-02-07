<?php

namespace TomShaw\Mediable\Components;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Livewire\Attributes\On;
use Livewire\{Component, WithPagination};
use TomShaw\Mediable\Concerns\{AlertState, ModalState, PanelState, ShowState};
use TomShaw\Mediable\Eloquent\Eloquent;
use TomShaw\Mediable\Enums\BrowserEvents;
use TomShaw\Mediable\Models\Attachment;
use TomShaw\Mediable\Traits\{ServerLimits, WithCache, WithColumnWidths, WithExtension, WithFileSize, WithMimeTypes, WithReporting};

class MediaBrowser extends Component
{
    use ServerLimits;
    use WithCache;
    use WithColumnWidths;
    use WithExtension;
    use WithFileSize;
    use WithMimeTypes;
    use WithPagination;
    use WithReporting;

    public AlertState $alert;

    public ModalState $modal;

    public PanelState $panel;

    public ShowState $show;

    public string $uniqueId = '';

    public bool $fullScreen = false;

    public array $uniqueMimeTypes = [];

    public string $selectedMimeType = '';

    public string $searchTerm = '';

    public array $searchColumns = ['title', 'caption', 'description', 'file_original_name'];

    public array $orderColumns = ['id' => 'ID', 'file_name' => 'Name', 'file_type' => 'Type', 'file_size' => 'Size', 'file_dir' => 'Directory', 'file_url' => 'URL', 'title' => 'Title', 'caption' => 'Caption', 'description' => 'Description', 'sort_order' => 'Sort Order', 'created_at' => 'Created At', 'updated_at' => 'Updated At'];

    public int $perPage = 25;

    public array $perPageValues = [10, 25, 50, 75, 100, 0];

    public string $orderBy = 'id';

    public string $orderDir = 'DESC';

    public array $orderDirValues = ['ASC' => 'Ascending', 'DESC' => 'Descending'];

    public function mount(): void
    {
        $this->generateUniqueId();

        $this->modal = new ModalState;

        $this->alert = new AlertState;

        $this->panel = new PanelState(thumbMode: true);

        $this->show = new ShowState;

        $this->resetModal();

        $this->hasExtension('gd');

        Eloquent::garbage();

        $this->deleteStoreAttachmentId();
    }

    public function boot(): void
    {
        $this->dispatch(
            'server:limits',
            maxUploadSize: $this->getMaxUploadSize(),
            maxFileUploads: $this->getMaxFileUploads(),
            maxUploadFileSize: $this->getMaxUploadFileSize(),
            postMaxSize: $this->getPostMaxSize(),
            memoryLimit: $this->getMemoryLimit(),
        );

        $this->uniqueMimeTypes = Eloquent::uniqueMimes();
    }

    #[On('mediable.open')]
    public function open(?string $id = null): void
    {
        $this->modal = new ModalState(true, $id ?? '');
    }

    #[On('mediable.close')]
    public function close(): void
    {
        $this->closeModal();
    }

    #[On('mediable.alert')]
    public function alert($event): void
    {
        $this->alert = new AlertState(
            show: true,
            type: $event['type'],
            message: $event['message']
        );
    }

    #[On('toolbar:enable-thumb-mode')]
    public function enableThumbMode(): self
    {
        $this->panel = new PanelState(thumbMode: true);

        return $this;
    }

    public function enablePreviewMode(): self
    {
        $this->panel = new PanelState(previewMode: true);

        return $this;
    }

    #[On('toolbar:enable-editor-mode')]
    public function enableEditorMode(): self
    {
        $this->panel = new PanelState(editorMode: true);

        return $this;
    }

    #[On('toolbar:enable-upload-mode')]
    public function enableUploadMode(): self
    {
        $this->panel = new PanelState(uploadMode: true);

        return $this;
    }

    #[On('toolbar:toggle-sidebar')]
    public function toggleSidebar(): self
    {
        $this->show = new ShowState(showSidebar: ! $this->show->isShowSidebar(), showMetaInfo: $this->show->isShowMetaInfo());

        return $this;
    }

    #[On('toolbar:toggle-meta-info')]
    public function toggleMetaInfo(): self
    {
        $this->show = new ShowState(showMetaInfo: ! $this->show->isShowMetaInfo(), showSidebar: $this->show->isShowSidebar());

        return $this;
    }

    #[On('uploads:completed')]
    public function handleUploadsCompleted(string $message): void
    {
        $this->alert = new AlertState(
            show: true,
            type: 'success',
            message: $message
        );

        $this->fill([
            'orderBy' => 'id',
            'orderDir' => 'DESC',
        ]);

        $this->enableThumbMode();
    }

    #[On('attachment:active-changed')]
    public function handleActiveAttachmentChanged(int $id): void
    {
        $this->enablePreviewMode();
    }

    #[On('attachment:active-cleared')]
    public function handleActiveAttachmentCleared(): void
    {
        $this->enableThumbMode();
    }

    #[On('toolbar:delete-attachment')]
    public function deleteAttachment(int $id): void
    {
        Eloquent::delete($id);

        $this->dispatch('attachments:remove-item', id: $id);

        $this->alert = new AlertState(
            show: true,
            type: 'success',
            message: 'Attachment deleted successfully!'
        );
    }

    #[On('delete.selected')]
    public function deleteSelected(array $selectedIds): void
    {
        Attachment::whereIn('id', $selectedIds)->delete();

        $count = count($selectedIds);

        $message = ($count > 1) ? "Deleted $count attachment(s) successfully!" : 'Deleted attachment successfully!';

        $this->alert = new AlertState(
            show: true,
            type: 'success',
            message: $message
        );

        $this->dispatch('attachments:clear-selected');
    }

    public function resetModal(): void
    {
        $this->dispatch('uploads:reset');
        $this->dispatch('attachments:clear-selected');
        $this->enableThumbMode();
        $this->resetPage();
    }

    #[On('panel:insert-media')]
    public function insertMedia(array $selectedIds): void
    {
        $selected = Attachment::whereIn('id', $selectedIds)->get()->toArray();

        if ($this->modal->hasElementId()) {
            $this->dispatch(BrowserEvents::INSERT->value, selected: $selected);
        } else {
            $this->dispatch(BrowserEvents::DEFAULT->value, $selected);
        }

        $this->closeModal();
    }

    public function closeModal(): void
    {
        $this->modal = new ModalState(
            show: false,
            elementId: ''
        );

        $this->resetModal();
    }

    public function expandModal(): void
    {
        $this->fullScreen = ! $this->fullScreen;
    }

    public function closeAlert(): void
    {
        $this->alert = new AlertState;
    }

    public function updatedFiles(): void
    {
        $this->validate(config('mediable.validation'));
    }

    public function updatedSelectedMimeType(): void
    {
        $this->resetPage();
    }

    #[On('toolbar:order-dir-changed')]
    public function handleOrderDirChanged(string $orderDir): void
    {
        $this->orderDir = $orderDir;
    }

    #[On('toolbar:order-by-changed')]
    public function handleOrderByChanged(string $orderBy): void
    {
        $this->orderBy = $orderBy;
    }

    #[On('toolbar:column-width-changed')]
    public function handleColumnWidthChanged(int $defaultColumnWidth): void
    {
        $this->defaultColumnWidth = $defaultColumnWidth;
    }

    #[On('toolbar:mime-type-changed')]
    public function handleMimeTypeChanged(string $selectedMimeType): void
    {
        $this->selectedMimeType = $selectedMimeType;
        $this->resetPage();
    }

    public function toggleOrderDir(): void
    {
        $this->orderDir = $this->orderDir === 'asc' ? 'desc' : 'asc';
    }

    public function updatingPage(): void
    {
        $this->dispatch('attachments:reset-audio');
    }

    #[On('form:editor-saved')]
    public function handleEditorSaved(): void
    {
        $this->resetPage();

        $this->enableThumbMode();
    }

    #[On('toolbar:close-image-editor')]
    public function closeImageEditor(): void
    {
        $this->enableThumbMode();
    }

    #[On('panel:confirm-delete')]
    public function handleConfirmDelete(array $selectedIds): void
    {
        $this->dispatch('mediable.confirm', type: 'delete.selected', message: 'Are you sure you want to delete selected attachments?', selectedIds: $selectedIds);
    }

    #[On('panel:unique-id-updated')]
    public function handleUniqueIdUpdated(string $uniqueId): void
    {
        $this->uniqueId = $uniqueId;
        $this->dispatch('panel:regenerate-unique-id', uniqueId: $uniqueId);
    }

    public function generateUniqueId(): void
    {
        $this->uniqueId = uniqid();
    }

    private function renderView(LengthAwarePaginator $paginator): \Illuminate\Contracts\View\View
    {
        /** @var view-string $view */
        $view = 'mediable::livewire.media-browser';

        return view($view, [
            'uniqueId' => $this->uniqueId,
            'data' => $paginator,
        ]);
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        Eloquent::query($this->orderBy, $this->orderDir, $this->selectedMimeType);

        Eloquent::search($this->searchTerm, $this->searchColumns);

        $paginator = Eloquent::paginate($this->perPage);

        if ($paginator->isEmpty()) {
            $this->enableUploadMode();
        }

        return $this->renderView($paginator);
    }
}
