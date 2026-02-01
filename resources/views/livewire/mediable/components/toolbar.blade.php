<?php

use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;
use Livewire\Component;
use TomShaw\Mediable\Concerns\{PanelState, ShowState};

new class extends Component {
    #[Reactive]
    public PanelState $panel;

    #[Reactive]
    public ShowState $show;

    #[Reactive]
    public Collection $data;

    #[Reactive]
    public array $orderColumns;

    #[Reactive]
    public array $columnWidths;

    #[Reactive]
    public array $uniqueMimeTypes;

    public string $orderBy = 'id';

    public string $orderDir = 'DESC';

    public int $defaultColumnWidth = 4;

    public string $selectedMimeType = '';

    public array $selectedIds = [];

    public ?int $activeId = null;

    public int $uploadFileCount = 0;

    #[On('uploads:files-changed')]
    public function handleFilesChanged(int $count): void
    {
        $this->uploadFileCount = $count;
    }

    #[On('attachments:selection-changed')]
    public function handleSelectionChanged(array $selectedIds, ?int $activeId): void
    {
        $this->selectedIds = $selectedIds;
        $this->activeId = $activeId;
    }

    #[On('attachment:active-changed')]
    public function handleActiveAttachmentChanged(int $id): void
    {
        $this->activeId = $id;
    }

    #[On('attachment:active-cleared')]
    public function handleActiveAttachmentCleared(): void
    {
        $this->activeId = null;
    }

    #[On('form:request-active-id')]
    public function handleFormRequestActiveId(): void
    {
        if ($this->activeId) {
            $this->dispatch('form:receive-active-id', id: $this->activeId);
        }
    }

    public function mount(
        string $orderBy = 'id',
        string $orderDir = 'DESC',
        int $defaultColumnWidth = 4,
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
        $this->dispatch('uploads:reset');
    }

    public function createAttachments(): void
    {
        $this->dispatch('uploads:submit');
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

    public function deleteAttachment(): void
    {
        if ($this->activeId) {
            $this->dispatch('toolbar:delete-attachment', id: $this->activeId);
        }
    }

    public function closeImageEditor(): void
    {
        $this->dispatch('toolbar:close-image-editor');
    }
}; ?>

<div class="flex items-center justify-between h-full w-full px-4">

  @if($panel->isUploadMode())
  <div class="flex items-center justify-start gap-1.5 xl:gap-2">
    @if(!$data->isEmpty())
    <button wire:click="enableThumbMode" class="group relative inline-flex h-7 w-7 items-center justify-center overflow-hidden rounded-full bg-[#555] font-medium text-xs tracking-wider text-neutral-50 transition active:scale-95">
      <x-icons.close />
    </button>
    @endif
  </div>
  @if($uploadFileCount >= 1)
  <div class="flex items-center justify-end gap-1.5 xl:gap-2">
    <button wire:click="clearFiles" class="group relative inline-flex items-center justify-center overflow-hidden rounded-full bg-[#555] py-1.5 px-4 font-medium text-xs tracking-wider text-neutral-50">
      <span class="absolute h-0 w-0 rounded-full bg-red-500 transition-all duration-300 group-hover:h-56 group-hover:w-32"></span>
      <span class="relative">Reset</span>
    </button>
    <button type="button" class="group relative inline-flex items-center justify-center overflow-hidden rounded-full bg-[#555] py-1.5 px-4 font-medium text-xs tracking-wider text-neutral-50" wire:click="createAttachments" wire:loading.attr="disabled">
      <span class="absolute h-0 w-0 rounded-full bg-blue-400 transition-all duration-300 group-hover:h-full group-hover:w-full"></span>
      <span class="relative" wire:loading.remove wire:target="createAttachments">Submit</span>
      <span class="relative" wire:loading wire:target="createAttachments">Processing...</span>
    </button>
  </div>
  @endif
  @endif

  @if($panel->isThumbMode())
  <div class="flex items-center justify-start gap-1.5 xl:gap-2">

    <button type="button" class="group relative inline-flex h-7 w-7 items-center justify-center overflow-hidden rounded-full bg-[#555] font-medium text-xs tracking-wider text-neutral-50" wire:click="toggleMetaInfo">
      <x-icons.arrow :show="$show->isShowMetaInfo()" direction="right" />
    </button>

    @if($show->isShowOrderBy() && $data->count())
    <select class="control-select" wire:model.live="orderBy">
      @foreach($orderColumns as $key => $value)
      <option value="{{$key}}">{{ $value }}</option>
      @endforeach
    </select>
    @endif

    @if($show->isShowOrderDir() && $data->count())
    <button type="button" class="relative flex items-center justify-between w-[80px] min-w-[80px] max-w-[80px] px-3 py-1.5 bg-[#555] rounded-full font-medium text-xs tracking-wider text-neutral-50 cursor-pointer transition-all duration-100 ease-in" wire:click="toggleOrderDir()">
      @if(strtoupper($orderDir) == 'ASC')
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
      </svg>
      @else
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
      </svg>
      @endif
      <span>{{ strtoupper($orderDir) }}</span>
    </button>
    @endif

    @if($show->isShowColumnWidth() && $data->count())
    <select class="control-select" wire:model.live="defaultColumnWidth">
      @foreach(array_reverse($columnWidths, true) as $key => $value)
      <option value="{{$key}}">{{ $value }}</option>
      @endforeach
    </select>
    @endif

    @if($show->isShowUniqueMimeTypes() && count($uniqueMimeTypes)>=1)
    <select class="control-select" wire:model.live="selectedMimeType">
      <option value="">Mimes</option>
      @foreach($uniqueMimeTypes as $mimeType)
      <option value="{{$mimeType}}">{{ $mimeType }}</option>
      @endforeach
    </select>
    @endif

  </div>
  <div class="flex items-center justify-end gap-1.5 xl:gap-2">

    @if ($show->isShowEditor() && $activeId)
    <button wire:click="enableEditorMode()" class="group relative inline-flex h-7 items-center justify-center rounded-full overflow-hidden bg-[#555] px-4 font-medium text-xs tracking-wider text-neutral-50"><span>Editor</span>
      <div class="w-0 translate-x-full pl-0 opacity-0 transition-all duration-200 group-hover:w-5 group-hover:translate-x-0 group-hover:pl-1 group-hover:opacity-100"><svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5">
          <path d="M8.14645 3.14645C8.34171 2.95118 8.65829 2.95118 8.85355 3.14645L12.8536 7.14645C13.0488 7.34171 13.0488 7.65829 12.8536 7.85355L8.85355 11.8536C8.65829 12.0488 8.34171 12.0488 8.14645 11.8536C7.95118 11.6583 7.95118 11.3417 8.14645 11.1464L11.2929 8H2.5C2.22386 8 2 7.77614 2 7.5C2 7.22386 2.22386 7 2.5 7H11.2929L8.14645 3.85355C7.95118 3.65829 7.95118 3.34171 8.14645 3.14645Z" fill="currentColor" fill-rule="evenodd" clip-rule="evenodd"></path>
        </svg></div>
    </button>
    @endif

    @if ($show->isShowUpload())
    <button wire:click="enableUploadMode" class="group relative inline-flex h-7 items-center justify-center rounded-full bg-[#555] py-1.5 pl-5 pr-7 font-medium text-xs tracking-wider text-neutral-50"><span class="z-10 pr-2">Uploads</span>
      <div class="absolute right-1 inline-flex h-6 w-6 items-center justify-end rounded-full bg-[#696969] transition-[width] group-hover:w-[calc(100%-8px)]">
        <div class="mr-0.5 flex items-center justify-center"><svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-neutral-50">
            <path d="M8.14645 3.14645C8.34171 2.95118 8.65829 2.95118 8.85355 3.14645L12.8536 7.14645C13.0488 7.34171 13.0488 7.65829 12.8536 7.85355L8.85355 11.8536C8.65829 12.0488 8.34171 12.0488 8.14645 11.8536C7.95118 11.6583 7.95118 11.3417 8.14645 11.1464L11.2929 8H2.5C2.22386 8 2 7.77614 2 7.5C2 7.22386 2.22386 7 2.5 7H11.2929L8.14645 3.85355C7.95118 3.65829 7.95118 3.34171 8.14645 3.14645Z" fill="currentColor" fill-rule="evenodd" clip-rule="evenodd"></path>
          </svg></div>
      </div>
    </button>
    @endif

    <button type="button" class="group relative inline-flex h-7 w-7 items-center justify-center overflow-hidden rounded-full bg-[#555] font-medium text-xs tracking-wider text-neutral-50" wire:click="toggleSidebar">
      <x-icons.arrow :show="$show->isShowSidebar()" direction="left" />
    </button>
  </div>
  @endif

  @if($panel->isPreviewMode())
  <div class="flex items-center justify-start gap-1.5 xl:gap-2">

    <button type="button" class="group relative inline-flex h-7 w-7 items-center justify-center overflow-hidden rounded-full bg-[#555] font-medium text-xs tracking-wider text-neutral-50" wire:click="toggleMetaInfo">
      <x-icons.arrow :show="$show->isShowMetaInfo()" direction="right" />
    </button>

    <button wire:click="enableThumbMode" class="group relative inline-flex h-7 w-7 items-center justify-center overflow-hidden rounded-full bg-[#555] font-medium text-xs tracking-wider text-neutral-50 transition active:scale-95">
      <x-icons.close />
    </button>

    <div wire:loading class="hidden">
      <div class="border-gray-300 h-6 w-6 animate-spin rounded-full border-2 border-t-blue-600"></div>
    </div>

  </div>
  <div class="flex flex-row items-center justify-end gap-1.5 xl:gap-2">

    @if($activeId)
    <button wire:click="deleteAttachment" class="group relative inline-flex items-center justify-center overflow-hidden rounded-full bg-[#555] py-1.5 px-4 font-medium text-xs tracking-wider text-neutral-50">
      <span class="absolute h-0 w-0 rounded-full bg-red-500 transition-all duration-300 group-hover:h-56 group-hover:w-32"></span>
      <span class="relative">Trash</span>
    </button>
    @endif

    @if ($show->isShowEditor() && $activeId)
    <button wire:click="enableEditorMode()" class="group relative inline-flex h-7 items-center justify-center overflow-hidden rounded-full bg-[#555] px-4 font-medium text-xs tracking-wider text-neutral-50"><span>Editor</span>
      <div class="w-0 translate-x-full pl-0 opacity-0 transition-all duration-200 group-hover:w-5 group-hover:translate-x-0 group-hover:pl-1 group-hover:opacity-100"><svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5">
          <path d="M8.14645 3.14645C8.34171 2.95118 8.65829 2.95118 8.85355 3.14645L12.8536 7.14645C13.0488 7.34171 13.0488 7.65829 12.8536 7.85355L8.85355 11.8536C8.65829 12.0488 8.34171 12.0488 8.14645 11.8536C7.95118 11.6583 7.95118 11.3417 8.14645 11.1464L11.2929 8H2.5C2.22386 8 2 7.77614 2 7.5C2 7.22386 2.22386 7 2.5 7H11.2929L8.14645 3.85355C7.95118 3.65829 7.95118 3.34171 8.14645 3.14645Z" fill="currentColor" fill-rule="evenodd" clip-rule="evenodd"></path>
        </svg></div>
    </button>
    @endif

    <button type="button" class="group relative inline-flex h-7 w-7 items-center justify-center overflow-hidden rounded-full bg-[#555] font-medium text-xs tracking-wider text-neutral-50" wire:click="toggleSidebar">
      <x-icons.arrow :show="$show->isShowSidebar()" direction="left" />
    </button>
  </div>
  @endif

  @if($panel->isEditorMode())
  <div class="flex items-center justify-start gap-1.5 xl:gap-2">
    <button type="button" class="group relative inline-flex h-7 w-7 items-center justify-center overflow-hidden rounded-full bg-[#555] font-medium text-xs tracking-wider text-neutral-50" wire:click="toggleMetaInfo">
      <x-icons.arrow :show="$show->isShowMetaInfo()" direction="right" />
    </button>

    <button wire:click="closeImageEditor" class="group relative inline-flex h-7 w-7 items-center justify-center overflow-hidden rounded-full bg-[#555] font-medium text-xs tracking-wider text-neutral-50 transition active:scale-95">
      <x-icons.close />
    </button>

    <div wire:loading class="hidden">
      <div class="border-gray-300 h-6 w-6 animate-spin rounded-full border-2 border-t-blue-600"></div>
    </div>
  </div>
  <div class="flex flex-row items-center justify-end gap-1.5 xl:gap-2">
    <button type="button" class="group relative inline-flex h-7 w-7 items-center justify-center overflow-hidden rounded-full bg-[#555] font-medium text-xs tracking-wider text-neutral-50" wire:click="toggleSidebar">
      <x-icons.arrow :show="$show->isShowSidebar()" direction="left" />
    </button>
  </div>
  @endif

</div>
