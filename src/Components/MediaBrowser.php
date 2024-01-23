<?php

namespace TomShaw\Mediable\Components;

use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use TomShaw\Mediable\Eloquent\Eloquent;
use TomShaw\Mediable\Enums\BrowserEvents;
use TomShaw\Mediable\Enums\ModalTypes;
use TomShaw\Mediable\Exceptions\MediaBrowserException;
use TomShaw\Mediable\Models\Attachment;
use TomShaw\Mediable\Traits\ServerLimits;
use TomShaw\Mediable\Traits\WithEvents;
use TomShaw\Mediable\Traits\WithFileSize;
use TomShaw\Mediable\Traits\WithMimeTypes;

class MediaBrowser extends Component
{
    use ServerLimits;
    use WithEvents;
    use WithFileSize;
    use WithFileUploads;
    use WithMimeTypes;
    use WithPagination;

    public string $theme = 'tailwind';

    public string $modalType = 'array';

    public bool $fullScreen = false;

    public array $files = [];

    public array $selected = [];

    public array $uniqueMimeTypes = [];

    public string $selectedMimeType = '';

    public ?int $modelId = null;

    public string $title = '';

    public ?string $caption = '';

    public ?string $description = '';

    public string $searchTerm = '';

    public array $searchColumns = ['title', 'caption', 'description', 'file_original_name'];

    public array $orderColumns = ['id' => 'ID', 'file_name' => 'Name', 'file_type' => 'Type', 'file_size' => 'Size', 'file_dir' => 'Directory', 'file_url' => 'URL', 'title' => 'Title', 'caption' => 'Caption', 'description' => 'Description', 'created_at' => 'Created At', 'updated_at' => 'Updated At'];

    public int $perPage = 25;

    public array $perPageValues = [10, 25, 50, 100, 0];

    public string $orderBy = 'id';

    public string $orderDir = 'DESC';

    public array $orderDirValues = ['ASC' => 'Ascending', 'DESC' => 'Descending'];

    public bool $thumbMode = true;

    public bool $tableMode = false;

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

    public function mount(?string $theme = null)
    {
        $this->theme = $theme ?? config('mediable.theme');

        $this->maxUploadSize = $this->getMaxUploadSize();
        $this->maxFileUploads = $this->getMaxFileUploads();
        $this->maxUploadFileSize = $this->getMaxUploadFileSize();
        $this->postMaxSize = $this->getPostMaxSize();
        $this->memoryLimit = $this->getMemoryLimit();

        $this->resetModal();
    }

    public function boot()
    {
        $this->dispatch('server:limits',
            maxUploadSize: $this->maxUploadSize,
            maxFileUploads: $this->maxFileUploads,
            maxUploadFileSize: $this->maxUploadFileSize,
            postMaxSize: $this->postMaxSize,
            memoryLimit: $this->memoryLimit
        );
    }

    #[On('modal:type')]
    public function setModalType($modalType): void
    {
        $this->modalType = $modalType;
    }

    #[On('audio:start')]
    public function playAudio($id): void
    {
        $this->audioElementId = $id;
    }

    #[On('audio:pause')]
    public function pauseAudio($id): void
    {
        if ($this->audioElementId == $id) {
            $this->audioElementId = null;
        }
    }

    #[On('media:alert')]
    public function alert($event): void
    {
        dd($event);
    }

    public function enableThumbMode(): self
    {
        $this->fill([
            'thumbMode' => true,
            'tableMode' => false,
            'uploadMode' => false,
        ]);

        return $this;
    }

    public function enableTableMode(): self
    {
        $this->fill([
            'thumbMode' => false,
            'tableMode' => true,
            'uploadMode' => false,
        ]);

        return $this;
    }

    public function enableUploadMode(): self
    {
        $this->fill([
            'thumbMode' => false,
            'tableMode' => false,
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

        $this->dispatchAlert('success', 'Item successfully updated!');
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

        $this->dispatchAlert('success', 'Item successfully deleted!');
    }

    public function toggleAttachment(Attachment $item): void
    {
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

        if (! $this->showSidebar) {
            $this->toggleSidebar();
        }
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
        if ($this->modalType == ModalTypes::ATTACHMENT->value) {
            $this->dispatch(BrowserEvents::INSERT->value, selected: $this->selected);
        } else {
            $this->dispatch(BrowserEvents::DEFAULT->value, $this->selected);
        }

        $this->closeModal();
    }

    public function closeModal(): void
    {
        $this->dispatch(BrowserEvents::CLOSE);

        $this->resetModal();
    }

    public function expandModal(): void
    {
        $this->fullScreen = ! $this->fullScreen;
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

    private function renderView(LengthAwarePaginator $paginator)
    {
        return view('mediable::'.$this->theme.'.modal', [
            'data' => $paginator,
        ]);
    }

    public function render()
    {
        $this->uniqueMimeTypes = Eloquent::uniqueMimes();

        Eloquent::query($this->orderBy, $this->orderDir, $this->selectedMimeType);

        Eloquent::search($this->searchTerm, $this->searchColumns);

        $paginator = Eloquent::paginate($this->perPage);

        return $this->renderView($paginator);
    }
}
