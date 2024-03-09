<?php

namespace TomShaw\Mediable\Components;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\On;
use Livewire\{Component, WithFileUploads, WithPagination};
use TomShaw\Mediable\Concerns\{AlertState, AttachmentState, ModalState, PanelState, ShowState};
use TomShaw\Mediable\Eloquent\Eloquent;
use TomShaw\Mediable\Enums\BrowserEvents;
use TomShaw\Mediable\Exceptions\MediaBrowserException;
use TomShaw\Mediable\Models\Attachment;
use TomShaw\Mediable\Traits\{ServerLimits, WithCache, WithExtension, WithFileSize, WithGraphicDraw, WithMimeTypes};

class MediaBrowser extends Component
{
    use ServerLimits;
    use WithCache;
    use WithExtension;
    use WithFileSize;
    use WithFileUploads;
    use WithGraphicDraw;
    use WithMimeTypes;
    use WithPagination;

    public AlertState $alert;

    public ModalState $modal;

    public AttachmentState $attachment;

    public PanelState $panel;

    public ShowState $show;

    public $uniqueId;

    public string $theme = 'tailwind';

    public bool $fullScreen = false;

    public array $files = [];

    public array $selected = [];

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

    public array $columnWidths = [100, 50, 33.3, 25, 20, 16.66, 14.28, 12.5, 11.11, 10, 9.09, 8.33];

    public int $defaultColumnWidth = 4;

    public ?int $audioElementId = null;

    public ?int $maxUploadSize = null;

    public ?int $maxFileUploads = null;

    public ?int $maxUploadFileSize = null;

    public ?int $postMaxSize = null;

    public ?int $memoryLimit = null;

    public int $imageHeight = 0;

    public int $imageWidth = 0;

    public int $imageType = 0;

    public function mount(?string $theme = null)
    {
        $this->generateUniqueId();

        $this->modal = new ModalState();

        $this->alert = new AlertState();

        $this->attachment = new AttachmentState();

        $this->panel = new PanelState(thumbMode: true);

        $this->show = new ShowState();

        $this->theme = $theme ?? config('mediable.theme');

        $this->maxUploadSize = $this->getMaxUploadSize();
        $this->maxFileUploads = $this->getMaxFileUploads();
        $this->maxUploadFileSize = $this->getMaxUploadFileSize();
        $this->postMaxSize = $this->getPostMaxSize();
        $this->memoryLimit = $this->getMemoryLimit();

        $this->resetModal();

        $this->hasExtension('gd');

        Eloquent::garbage();

        if ($this->hasStoreAttachmentId()) {
            $id = $this->getStoreAttachmentId();
            $this->toggleAttachment($id);
        }
    }

    public function boot()
    {
        $this->dispatch(
            'server:limits',
            maxUploadSize: $this->maxUploadSize,
            maxFileUploads: $this->maxFileUploads,
            maxUploadFileSize: $this->maxUploadFileSize,
            postMaxSize: $this->postMaxSize,
            memoryLimit: $this->memoryLimit
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

    #[On('audio.start')]
    public function playAudio($id): void
    {
        $this->audioElementId = $id;
    }

    #[On('audio.pause')]
    public function pauseAudio($id): void
    {
        if ($this->audioElementId == $id) {
            $this->audioElementId = null;
        }
    }

    #[On('media.alert')]
    public function alert($event): void
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

        $this->prepareImageEditor();

        return $this;
    }

    public function enableUploadMode(): self
    {
        $this->panel = new PanelState(uploadMode: true);

        return $this;
    }

    public function toggleSidebar(): self
    {
        $this->show = new ShowState(showSidebar: ! $this->show->isShowSidebar(), showMetaInfo: $this->show->isShowMetaInfo());

        return $this;
    }

    public function toggleMetaInfo(): self
    {
        $this->show = new ShowState(showMetaInfo: ! $this->show->isShowMetaInfo(), showSidebar: $this->show->isShowSidebar());

        return $this;
    }

    public function createAttachments(): void
    {
        Eloquent::create($this->files);

        $count = count($this->files);
        $message = (count($this->files) > 1) ? "Created $count attachment(s) successfully!" : 'Created attachment successfully!';

        $this->alert = new AlertState(
            show: true,
            type: 'success',
            message: $message
        );

        $this->fill([
            'files' => [],
            'orderBy' => 'id',
            'orderDir' => 'DESC',
        ]);

        $this->enableThumbMode();
    }

    public function updateAttachment(): void
    {
        $data = [
            'title' => $this->attachment->title,
            'caption' => $this->attachment->caption,
            'description' => $this->attachment->description,
            'sort_order' => $this->attachment->sort_order,
            'styles' => $this->attachment->styles,
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
        } else {
            $validated = $validator->validated();

            Eloquent::update($this->attachment->id, $validated);

            $this->alert = new AlertState(
                show: true,
                type: 'success',
                message: 'Attachment updated successfully!'
            );
        }
    }

    public function deleteAttachment(int $id): void
    {
        Eloquent::delete($id);

        $this->clearSelected();

        $this->attachment = new AttachmentState();

        $this->alert = new AlertState(
            show: true,
            type: 'success',
            message: 'Attachment deleted successfully!'
        );

        $this->enableThumbMode();
    }

    public function toggleAttachment(int $id): void
    {
        $item = Attachment::find($id);

        if (! $item) {
            throw new MediaBrowserException("No attachment found with id: $id");
        }

        $found = in_array($item['id'], array_column($this->selected, 'id'));

        if ($found) {
            foreach ($this->selected as $key => $value) {
                if ($value['id'] === $item['id']) {
                    unset($this->selected[$key]);
                    break;
                }
            }
        } else {
            array_push($this->selected, $item);
        }

        $this->attachment = AttachmentState::fromAttachment($item);

        if ($this->panel->isPreviewMode()) {
            $this->enableThumbMode();
        }

        $this->applyImageInfo($item);

        $this->dispatch('mediable.scroll', id: $this->attachment->id);

        $this->alert = new AlertState();

        $this->storeAttachmentId($this->attachment->id);
    }

    public function setActiveAttachment(Attachment $item): void
    {
        $found = in_array($item['id'], array_column($this->selected, 'id'));

        if (! $found) {
            return;
        }

        $this->attachment = AttachmentState::fromAttachment($item);

        $this->applyImageInfo($item);

        $this->enablePreviewMode();

        $this->alert = new AlertState();

        $this->storeAttachmentId($this->attachment->id);
    }

    public function clearSelected(): void
    {
        $this->selected = [];
    }

    public function confirmDelete()
    {
        $this->dispatch('mediable.confirm', type: 'delete.selected', message: 'Are you sure you want to delete selected attachments?');
    }

    #[On('delete.selected')]
    public function deleteSelected(): void
    {
        foreach ($this->selected as $item) {
            Eloquent::delete($item['id']);
        }

        $count = count($this->selected);

        $message = ($count > 1) ? "Deleted $count attachment(s) successfully!" : 'Deleted attachment successfully!';

        $this->alert = new AlertState(
            show: true,
            type: 'success',
            message: $message
        );

        $this->attachment = new AttachmentState();

        $this->clearSelected();
    }

    public function resetModal(): void
    {
        $this->clearFiles();
        $this->clearSelected();
        $this->enableThumbMode();
        $this->resetPage();
    }

    public function insertMedia(): void
    {
        if ($this->modal->hasElementId()) {
            $this->dispatch(BrowserEvents::INSERT->value, selected: $this->selected);
        } else {
            $this->dispatch(BrowserEvents::DEFAULT->value, $this->selected);
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
        $this->alert = new AlertState();
    }

    public function updatedFiles(): void
    {
        $this->validate(config('mediable.validation'));
    }

    public function updatedSelectedMimeType(): void
    {
        $this->resetPage();
    }

    public function clearFile(int $index): void
    {
        try {
            array_splice($this->files, $index, 1);
        } catch (MediaBrowserException $e) {
            throw new MediaBrowserException($e->getMessage());
        }
    }

    public function clearFiles(): void
    {
        $this->files = [];
    }

    public function toggleOrderDir()
    {
        $this->orderDir = $this->orderDir === 'asc' ? 'desc' : 'asc';
    }

    public function getTotalUploadSize()
    {
        return array_reduce($this->files, function ($carry, $file) {
            return $carry + $file->getSize();
        }, 0);
    }

    public function resetAudioElement()
    {
        if ($this->audioElementId) {
            $this->audioElementId = null;
        }
    }

    public function updatingPage(): void
    {
        $this->resetAudioElement();
    }

    public function applyImageInfo($item): void
    {
        if ($this->mimeTypeImage($item['file_type'])) {
            [$width, $height, $type] = $this->getImageSize(Eloquent::getFilePath($item['file_dir']));

            if ($type) {
                $this->fill([
                    'imageWidth' => $width,
                    'imageHeight' => $height,
                    'newWidth' => $width,
                    'newHeight' => $height,
                    'imageType' => $type,
                    'scaleMode' => null,
                ]);
            }
        }
    }

    public function prepareImageEditor(): void
    {
        if (! $this->panel->IsEditorMode()) {
            return;
        }

        $this->primaryId = $this->attachment->getId();

        $source = $this->attachment->getFileDir();

        $destination = Eloquent::randomizeName($source);

        $destination = Eloquent::copyImageFromTo($source, $destination);

        $item = Eloquent::saveImageToDatabase($this->attachment, $destination);

        $this->attachment = AttachmentState::fromAttachment($item);

        $this->clearSelected();

        $this->resetPage();

        $this->editHistory = [];
    }

    public function saveEditorChanges()
    {
        Eloquent::enable($this->attachment->id);

        $this->fillEditorProperties();

        $this->editHistory = [];

        $this->primaryId = null;

        $this->enableThumbMode();

        $this->resetPage();
    }

    public function undoEditorChanges()
    {
        $this->fillEditorProperties();

        $row = Eloquent::load($this->primaryId);

        $this->primaryId = $row->id;

        $source = $row->file_dir;

        $destination = Eloquent::randomizeName($source);

        $destination = Eloquent::copyImageFromTo($source, $destination);

        $item = Eloquent::saveImageToDatabase($this->attachment, $destination);

        $this->attachment = AttachmentState::fromAttachment($item);

        $this->clearSelected();

        $this->resetPage();

        $this->editHistory = [];
    }

    public function closeImageEditor()
    {
        $this->fillEditorProperties();

        $this->editHistory = [];

        $this->primaryId = null;

        $this->enableThumbMode();
    }

    public function fillEditorProperties()
    {
        $this->fill([
            'flipMode' => null,
            'filterMode' => null,
            'contrast' => 0,
            'brightness' => 0,
            'colorize' => null,
            'colorizeRed' => -50,
            'colorizeGreen' => -50,
            'colorizeBlue' => 50,
            'smoothLevel' => 0,
            'pixelateBlockSize' => 1,
            'newWidth' => 100,
            'newHeight' => -1,
            'scaleMode' => null,
        ]);
    }

    public function generateUniqueId()
    {
        $this->uniqueId = uniqid();
    }

    private function renderView(LengthAwarePaginator $paginator)
    {
        return view('mediable::'.$this->theme.'.media-browser', [
            'uniqueId' => $this->uniqueId,
            'data' => $paginator,
        ]);
    }

    public function render()
    {
        Eloquent::query($this->orderBy, $this->orderDir, $this->selectedMimeType);

        Eloquent::search($this->searchTerm, $this->searchColumns);

        $paginator = Eloquent::paginate($this->perPage);

        return $this->renderView($paginator);
    }
}
