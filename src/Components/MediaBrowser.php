<?php

namespace TomShaw\Mediable\Components;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\{Computed, On};
use Livewire\{Component, WithPagination};
use TomShaw\Mediable\Concerns\{AlertState, AttachmentState, ModalState, PanelState, ShowState};
use TomShaw\Mediable\Eloquent\Eloquent;
use TomShaw\Mediable\Enums\BrowserEvents;
use TomShaw\Mediable\GraphicDraw\GraphicDraw;
use TomShaw\Mediable\Models\Attachment;
use TomShaw\Mediable\Traits\{WithCache, WithColumnWidths, WithExtension, WithFileSize, WithMimeTypes, WithReporting};

/**
 * @property-read LengthAwarePaginator<int, Attachment> $paginator
 * @property-read list<string> $uniqueMimeTypes
 * @property-read AttachmentState|null $activeAttachment
 * @property-read Collection<int, Attachment> $selectedAttachments
 * @property-read AttachmentState|null $editorAttachment
 * @property-read array{width: int, height: int}|null $activeImageDimensions
 */
class MediaBrowser extends Component
{
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

    public bool $fullScreen = false;

    public string $selectedMimeType = '';

    public string $searchTerm = '';

    /** @var list<string> */
    public array $searchColumns = ['title', 'caption', 'description', 'file_original_name'];

    /** @var array<string, string> */
    public array $orderColumns = ['id' => 'ID', 'file_name' => 'Name', 'file_type' => 'Type', 'file_size' => 'Size', 'file_dir' => 'Directory', 'file_url' => 'URL', 'title' => 'Title', 'caption' => 'Caption', 'description' => 'Description', 'sort_order' => 'Sort Order', 'created_at' => 'Created At', 'updated_at' => 'Updated At'];

    public int $perPage = 25;

    /** @var list<int> */
    public array $perPageValues = [10, 25, 50, 75, 100, 0];

    public string $orderBy = 'id';

    public string $orderDir = 'DESC';

    /** @var array<string, string> */
    public array $orderDirValues = ['ASC' => 'Ascending', 'DESC' => 'Descending'];

    /** @var list<int> */
    public array $selectedIds = [];

    public ?int $activeId = null;

    public ?int $audioElementId = null;

    public ?int $editorId = null;

    public int $editorVersion = 0;

    public string $title = '';

    public string $caption = '';

    public int $sort_order = 0;

    public string $styles = '';

    public string $description = '';

    public function mount(): void
    {
        $this->modal = new ModalState;

        $this->alert = new AlertState;

        $this->panel = new PanelState(thumbMode: true);

        $this->show = new ShowState;

        $this->hasExtension('gd');

        Eloquent::garbage();

        $this->deleteStoreAttachmentId();

        $this->resetModal();
    }

    /**
     * @return LengthAwarePaginator<int, Attachment>
     */
    #[Computed]
    public function paginator(): LengthAwarePaginator
    {
        Eloquent::query($this->orderBy, $this->orderDir, $this->selectedMimeType);

        Eloquent::search($this->searchTerm, $this->searchColumns);

        return Eloquent::paginate($this->perPage);
    }

    /**
     * @return list<string>
     */
    #[Computed]
    public function uniqueMimeTypes(): array
    {
        return Eloquent::uniqueMimes();
    }

    #[Computed]
    public function activeAttachment(): ?AttachmentState
    {
        if (! $this->activeId) {
            return null;
        }

        $item = Attachment::find($this->activeId);

        return $item ? AttachmentState::fromAttachment($item) : null;
    }

    /**
     * @return Collection<int, Attachment>
     */
    #[Computed]
    public function selectedAttachments(): Collection
    {
        if ($this->selectedIds === []) {
            return new Collection;
        }

        return Attachment::whereIn('id', $this->selectedIds)->get();
    }

    #[Computed]
    public function editorAttachment(): ?AttachmentState
    {
        if (! $this->editorId) {
            return null;
        }

        $item = Attachment::find($this->editorId);

        return $item ? AttachmentState::fromAttachment($item) : null;
    }

    /**
     * @return array{width: int, height: int}|null
     */
    #[Computed]
    public function activeImageDimensions(): ?array
    {
        $attachment = $this->activeAttachment;

        if (! $attachment?->file_type || ! str_starts_with($attachment->file_type, 'image/')) {
            return null;
        }

        $filePath = Eloquent::getFilePath($attachment->file_dir);

        if (! file_exists($filePath)) {
            return null;
        }

        $info = GraphicDraw::getimagesize($filePath);

        if ($info === false) {
            return null;
        }

        [$width, $height, $type] = $info;

        return $type ? ['width' => (int) $width, 'height' => (int) $height] : null;
    }

    public function cacheKey(Carbon|string|null $updatedAt): string
    {
        return $updatedAt ? (string) Carbon::parse($updatedAt)->getTimestamp() : '0';
    }

    #[On(BrowserEvents::OPEN->value)]
    public function open(?string $id = null): void
    {
        $this->modal = new ModalState(true, $id ?? '');
    }

    #[On(BrowserEvents::CLOSE->value)]
    public function close(): void
    {
        $this->closeModal();
    }

    /**
     * @param  array{type: string, message: string}  $event
     */
    #[On(BrowserEvents::ALERT->value)]
    public function alert(array $event): void
    {
        $this->alert = new AlertState(
            show: true,
            type: $event['type'],
            message: $event['message']
        );
    }

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

    public function enableEditorMode(): self
    {
        $this->panel = new PanelState(editorMode: true);

        return $this;
    }

    public function enableUploadMode(): self
    {
        $this->panel = new PanelState(uploadMode: true);

        return $this;
    }

    public function toggleSidebar(): self
    {
        $this->show = $this->show->toggleSidebar();

        return $this;
    }

    public function toggleMetaInfo(): self
    {
        $this->show = $this->show->toggleMetaInfo();

        return $this;
    }

    public function toggleAttachment(int $id): void
    {
        if (in_array($id, $this->selectedIds)) {
            $this->selectedIds = array_values(array_diff($this->selectedIds, [$id]));
        } else {
            $this->selectedIds[] = $id;
        }

        $this->setActive($id);

        $this->dispatch(BrowserEvents::SCROLL->value, id: $id);
    }

    public function setActiveAttachment(int $id): void
    {
        if ($this->activeId === $id) {
            $this->setActive(null);
            $this->enableThumbMode();
        } else {
            $this->setActive($id);
            $this->enablePreviewMode();
        }
    }

    public function clearSelected(): void
    {
        $this->selectedIds = [];

        $this->setActive(null);
    }

    public function playAudio(int $id): void
    {
        $this->audioElementId = $id;

        $this->dispatch(BrowserEvents::AUDIO_START->value, id: $id);
    }

    public function pauseAudio(int $id): void
    {
        if ($this->audioElementId === $id) {
            $this->audioElementId = null;
        }

        $this->dispatch(BrowserEvents::AUDIO_PAUSE->value, id: $id);
    }

    public function deleteAttachment(int $id): void
    {
        Eloquent::delete($id);

        $this->selectedIds = array_values(array_diff($this->selectedIds, [$id]));

        if ($this->activeId === $id) {
            $this->setActive($this->selectedIds === [] ? null : array_last($this->selectedIds));
        }

        unset($this->paginator, $this->selectedAttachments, $this->uniqueMimeTypes);

        $this->alert = new AlertState(
            show: true,
            type: 'success',
            message: 'Attachment deleted successfully!'
        );
    }

    public function confirmDelete(): void
    {
        $this->dispatch(BrowserEvents::CONFIRM->value,
            message: 'Are you sure you want to delete the selected attachments?',
            type: BrowserEvents::DELETE_SELECTED->value,
            selectedIds: $this->selectedIds,
        );
    }

    /**
     * @param  list<int>  $selectedIds
     */
    #[On(BrowserEvents::DELETE_SELECTED->value)]
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

        $this->clearSelected();

        unset($this->paginator, $this->selectedAttachments, $this->uniqueMimeTypes);
    }

    public function updateAttachment(): void
    {
        if (! $this->activeId) {
            return;
        }

        $data = [
            'title' => $this->title,
            'caption' => $this->caption,
            'sort_order' => $this->sort_order,
            'styles' => $this->styles,
            'description' => $this->description,
        ];

        $rules = [
            'title' => 'required|string|max:255',
            'caption' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'sort_order' => 'required|integer',
            'styles' => 'nullable|string|max:500',
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            $this->alert = new AlertState(
                show: true,
                type: 'error',
                message: $validator->errors()->first()
            );

            return;
        }

        Eloquent::update($this->activeId, $data);

        unset($this->paginator, $this->activeAttachment, $this->selectedAttachments);

        $this->alert = new AlertState(
            show: true,
            type: 'success',
            message: 'Attachment updated successfully!'
        );
    }

    public function resetModal(): void
    {
        $this->dispatch(BrowserEvents::UPLOADS_RESET->value);
        $this->clearSelected();
        $this->enableThumbMode();
        $this->resetPage();
    }

    /**
     * @param  list<int>|null  $selectedIds
     */
    public function insertMedia(?array $selectedIds = null): void
    {
        $selected = Attachment::whereIn('id', $selectedIds ?? $this->selectedIds)->get()->toArray();

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

    public function updatedSelectedMimeType(): void
    {
        $this->resetPage();
    }

    public function updatedSearchTerm(): void
    {
        $this->resetPage();
    }

    public function toggleOrderDir(): void
    {
        $this->orderDir = strtoupper($this->orderDir) === 'ASC' ? 'DESC' : 'ASC';
    }

    public function updatingPage(): void
    {
        $this->audioElementId = null;
    }

    #[On(BrowserEvents::UPLOADS_COMPLETED->value)]
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

        unset($this->paginator, $this->uniqueMimeTypes);

        $this->enableThumbMode();
    }

    #[On(BrowserEvents::EDITOR_ATTACHMENT_UPDATED->value)]
    public function handleEditorAttachmentUpdated(int $id, int $version = 0): void
    {
        $this->editorId = $id;
        $this->editorVersion = $version;

        unset($this->editorAttachment);
    }

    #[On(BrowserEvents::FORM_EDITOR_SAVED->value)]
    public function handleEditorSaved(): void
    {
        $this->editorId = null;
        $this->editorVersion = 0;

        unset($this->paginator, $this->editorAttachment);

        $this->resetPage();

        $this->enableThumbMode();
    }

    public function closeImageEditor(): void
    {
        $this->editorId = null;
        $this->editorVersion = 0;

        $this->enableThumbMode();
    }

    protected function setActive(?int $id): void
    {
        $this->activeId = $id;

        unset($this->activeAttachment, $this->activeImageDimensions);

        $this->syncDraftFromActive();
    }

    protected function syncDraftFromActive(): void
    {
        $attachment = $this->activeAttachment;

        $this->title = $attachment->title ?? '';
        $this->caption = $attachment->caption ?? '';
        $this->sort_order = $attachment->sort_order ?? 0;
        $this->styles = $attachment->styles ?? '';
        $this->description = $attachment->description ?? '';
    }

    public function render(): View
    {
        if ($this->paginator->isEmpty()) {
            $this->enableUploadMode();
        }

        /** @var view-string $view */
        $view = 'mediable::livewire.media-browser';

        return view($view);
    }
}
