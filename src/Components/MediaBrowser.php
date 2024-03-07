<?php

namespace TomShaw\Mediable\Components;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Livewire\Attributes\On;
use Livewire\{Component, WithFileUploads, WithPagination};
use TomShaw\Mediable\Concerns\{ModalAlert, ModalState, ModelState};
use TomShaw\Mediable\Eloquent\Eloquent;
use TomShaw\Mediable\Enums\BrowserEvents;
use TomShaw\Mediable\Exceptions\MediaBrowserException;
use TomShaw\Mediable\Models\Attachment;
use TomShaw\Mediable\Traits\{ServerLimits, WithExtension, WithFileSize, WithGraphicDraw, WithMimeTypes};

class MediaBrowser extends Component
{
    use ServerLimits;
    use WithExtension;
    use WithFileSize;
    use WithFileUploads;
    use WithGraphicDraw;
    use WithMimeTypes;
    use WithPagination;

    public ModalState $state;

    public ModalAlert $alert;

    public ModelState $model;

    public $uniqueId;

    public string $theme = 'tailwind';

    public bool $fullScreen = false;

    public array $files = [];

    public array $selected = [];

    public array $uniqueMimeTypes = [];

    public string $selectedMimeType = '';

    public string $searchTerm = '';

    public array $searchColumns = ['title', 'caption', 'description', 'file_original_name'];

    public array $orderColumns = ['id' => 'ID', 'file_name' => 'Name', 'file_type' => 'Type', 'file_size' => 'Size', 'file_dir' => 'Directory', 'file_url' => 'URL', 'title' => 'Title', 'caption' => 'Caption', 'description' => 'Description', 'created_at' => 'Created At', 'updated_at' => 'Updated At'];

    public int $perPage = 25;

    public array $perPageValues = [10, 25, 50, 100, 0];

    public string $orderBy = 'id';

    public string $orderDir = 'DESC';

    public array $orderDirValues = ['ASC' => 'Ascending', 'DESC' => 'Descending'];

    public bool $thumbMode = true;

    public bool $previewMode = false;

    public bool $uploadMode = false;

    public bool $editorMode = false;

    public bool $formMode = false;

    public bool $showPagination = true;

    public bool $showPerPage = true;

    public bool $showOrderBy = true;

    public bool $showOrderDir = true;

    public bool $showColumnWidth = true;

    public bool $showUniqueMimeTypes = true;

    public bool $showSidebar = true;

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

        $this->state = new ModalState();

        $this->alert = new ModalAlert();

        $this->model = new ModelState();

        $this->theme = $theme ?? config('mediable.theme');

        $this->maxUploadSize = $this->getMaxUploadSize();
        $this->maxFileUploads = $this->getMaxFileUploads();
        $this->maxUploadFileSize = $this->getMaxUploadFileSize();
        $this->postMaxSize = $this->getPostMaxSize();
        $this->memoryLimit = $this->getMemoryLimit();

        $this->resetModal();

        $this->hasExtension('gd');
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
        $this->state = new ModalState(true, $id ?? '');
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
        $this->alert = new ModalAlert(
            show: true,
            type: $event['type'],
            message: $event['message']
        );
    }

    public function enableThumbMode(): self
    {
        $this->fill([
            'thumbMode' => true,
            'previewMode' => false,
            'uploadMode' => false,
            'editorMode' => false,
        ]);

        return $this;
    }

    public function enablePreviewMode(): self
    {
        $this->fill([
            'thumbMode' => false,
            'previewMode' => true,
            'uploadMode' => false,
            'editorMode' => false,
        ]);

        return $this;
    }

    public function enableEditorMode(): self
    {
        $this->fill([
            'thumbMode' => false,
            'previewMode' => false,
            'uploadMode' => false,
            'editorMode' => true,
        ]);

        $this->prepareImageForEditor();

        return $this;
    }

    public function enableUploadMode(): self
    {
        $this->fill([
            'thumbMode' => false,
            'previewMode' => false,
            'uploadMode' => true,
            'editorMode' => false,
        ]);

        return $this;
    }

    public function toggleSidebar(): self
    {
        $this->showSidebar = ! $this->showSidebar;

        return $this;
    }

    public function createAttachments(): void
    {
        Eloquent::create($this->files);

        $message = count($this->files) ? 'Created attachment(s) successfully!' : 'Created attachment successfully!';

        $this->alert = new ModalAlert(
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
        Eloquent::update($this->model->id, $this->model->title, $this->model->caption, $this->model->description);

        $this->alert = new ModalAlert(
            show: true,
            type: 'success',
            message: 'Updated attachment successfully!'
        );
    }

    public function deleteAttachment(int $id): void
    {
        Eloquent::delete($id);

        $this->clearSelected();

        $this->model = new ModelState();

        $this->alert = new ModalAlert(
            show: true,
            type: 'success',
            message: 'Deleted attachment successfully!'
        );
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

        $this->model = ModelState::fromAttachment($item);

        if (! $this->showSidebar) {
            $this->toggleSidebar();
        }

        if ($this->mimeTypeImage($item['file_type'])) {
            [$width, $height, $type] = $this->getImageSize(Eloquent::getFilePath($item['file_dir']));

            if ($type) {
                $this->fill([
                    'imageWidth' => $width,
                    'imageHeight' => $height,
                    'newWidth' => $width,
                    'newHeight' => $height,
                    'imageType' => $type,
                    'scaleMode' => '',
                ]);
            }
        }

        $this->dispatch('mediable.scrollto', id: $this->model->id);

        $this->alert = new ModalAlert();
    }

    public function setActiveAttachment(Attachment $item): void
    {
        $found = in_array($item['id'], array_column($this->selected, 'id'));

        if (! $found) {
            return;
        }

        $this->model = ModelState::fromAttachment($item);

        if ($this->showSidebar) {
            $this->toggleSidebar();
        }

        if ($this->mimeTypeImage($item['file_type'])) {
            $size = $this->getImageSize(Eloquent::getFilePath($item['file_dir']));

            if (count($size)) {
                $this->fill([
                    'imageWidth' => $size[0],
                    'imageHeight' => $size[1],
                    'newWidth' => $size[0],
                    'newHeight' => $size[1],
                    'imageType' => $size[2],
                    'scaleMode' => '',
                ]);
            }
        }

        $this->enablePreviewMode();

        $this->alert = new ModalAlert();
    }

    public function clearSelected(): void
    {
        $this->selected = [];
    }

    public function deleteSelected(): void
    {
        foreach ($this->selected as $item) {
            Eloquent::delete($item['id']);
        }

        $count = count($this->selected);

        $message = ($count > 1) ? "Deleted $count attachment(s) successfully!" : 'Deleted attachment successfully!';

        $this->alert = new ModalAlert(
            show: true,
            type: 'success',
            message: $message
        );

        $this->model = new ModelState();

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
        if ($this->state->hasElementId()) {
            $this->dispatch(BrowserEvents::INSERT->value, selected: $this->selected);
        } else {
            $this->dispatch(BrowserEvents::DEFAULT->value, $this->selected);
        }

        $this->closeModal();
    }

    public function closeModal(): void
    {
        $this->state = new ModalState(
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
        $this->alert = new ModalAlert();
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

    public function prepareImageForEditor(): void
    {
        if (! $this->editorMode) {
            return;
        }

        $source = $this->model->fileDir;

        $destination = Eloquent::randomizeName($source);

        Eloquent::copyImageFromTo($source, $destination);

        $item = Eloquent::saveImageToDatabase($this->model, $destination);

        $this->model = ModelState::fromAttachment($item);

        $this->clearSelected();

        $this->resetPage();
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
