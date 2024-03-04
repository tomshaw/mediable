<?php

namespace TomShaw\Mediable\Components;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Livewire\Attributes\On;
use Livewire\{Component, WithFileUploads, WithPagination};
use TomShaw\Mediable\Concerns\{ModalAlert, ModalState};
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

    public $uniqueId;

    public string $theme = 'tailwind';

    public bool $fullScreen = false;

    public array $files = [];

    public array $selected = [];

    public array $uniqueMimeTypes = [];

    public string $selectedMimeType = '';

    public ?int $modelId = null;

    public string $title = '';

    public ?string $caption = '';

    public ?string $description = '';

    public ?string $fileUrl = '';

    public ?string $fileDir = '';

    public ?string $fileType = '';

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

    public bool $showPagination = true;

    public bool $showPerPage = true;

    public bool $showOrderBy = true;

    public bool $showOrderDir = true;

    public bool $showColumnWidth = true;

    public bool $showUniqueMimeTypes = true;

    public bool $showSidebar = true;

    public array $columnWidths = [100, 50, 33.3, 25, 20, 16.66, 14.28, 12.5, 11.11, 10, 9.09, 8.33];

    public int $defaultColumnWidth = 5;

    public ?int $audioElementId = null;

    public ?int $maxUploadSize = null;

    public ?int $maxFileUploads = null;

    public ?int $maxUploadFileSize = null;

    public ?int $postMaxSize = null;

    public ?int $memoryLimit = null;

    public int $imageWidth = 100;

    public function mount(?string $theme = null)
    {
        $this->generateUniqueId();

        $this->state = new ModalState();

        $this->alert = new ModalAlert();

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
        ]);

        return $this;
    }

    public function enablePreviewMode(): self
    {
        $this->fill([
            'thumbMode' => false,
            'previewMode' => true,
            'uploadMode' => false,
            'imageWidth' => 100,
        ]);

        return $this;
    }

    public function enableUploadMode(): self
    {
        $this->fill([
            'thumbMode' => false,
            'previewMode' => false,
            'uploadMode' => true,
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
        Eloquent::update($this->modelId, $this->title, $this->caption, $this->description);

        $this->alert = new ModalAlert(
            show: true,
            type: 'success',
            message: 'Updated attachment successfully!'
        );
    }

    public function deleteAttachment(int $id): void
    {
        Eloquent::delete($id);

        $this->fill([
            'selected' => [],
            'modelId' => null,
            'title' => '',
            'caption' => '',
            'description' => '',
        ]);

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

        $this->modelId = $item['id'];
        $this->title = $item['title'];
        $this->caption = $item['caption'];
        $this->description = $item['description'];
        $this->fileUrl = $item['file_url'];
        $this->fileType = $item['file_type'];
        $this->fileDir = $item['file_dir'];

        if (! $this->showSidebar) {
            $this->toggleSidebar();
        }

        $this->alert = new ModalAlert();
    }

    public function setActiveAttachment(Attachment $item): void
    {
        $found = in_array($item['id'], array_column($this->selected, 'id'));

        if (! $found) {
            return;
        }

        $this->modelId = $item['id'];
        $this->title = $item['title'];
        $this->caption = $item['caption'];
        $this->description = $item['description'];
        $this->fileUrl = $item['file_url'];
        $this->fileType = $item['file_type'];
        $this->fileDir = $item['file_dir'];

        if ($this->showSidebar) {
            $this->toggleSidebar();
        }

        $this->enablePreviewMode();

        $this->alert = new ModalAlert();
    }

    public function clearSelected(): void
    {
        $this->selected = [];

        if ($this->previewMode) {
            $this->enableThumbMode();
        }
    }

    public function deleteSelected(): void
    {
        foreach ($this->selected as $item) {
            Eloquent::delete($item['id']);
        }

        $count = count($this->selected);

        $message = $count ? "Deleted $count attachment(s) successfully!" : 'Deleted attachment successfully!';

        $this->alert = new ModalAlert(
            show: true,
            type: 'success',
            message: $message
        );

        $this->fill([
            'selected' => [],
            'modelId' => null,
            'title' => '',
            'caption' => '',
            'description' => '',
        ]);
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

    public function updatedImageWidth($value)
    {
        $this->imageWidth = $value;
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
