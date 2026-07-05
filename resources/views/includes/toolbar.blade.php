<div class="flex items-center justify-between h-full w-full px-4">

  {{-- Left Section --}}
  <div class="flex items-center justify-start gap-1.5 xl:gap-2">

    {{-- Meta Toggle (thumb, preview, editor modes) --}}
    @if(!$panel->isUploadMode())
    <button type="button" class="group relative inline-flex h-7 w-7 items-center justify-center overflow-hidden rounded-full bg-neutral-900 font-medium text-xs tracking-wider text-gray-50 cursor-pointer" wire:click="toggleMetaInfo">
      <x-icons.arrow :direction="$show->isShowMetaInfo() ? 'left' : 'right'" />
    </button>
    @endif

    {{-- Close/Back Button --}}
    @if($panel->isUploadMode() && !$this->paginator->isEmpty())
    <button wire:click="enableThumbMode" class="group relative inline-flex h-7 w-7 items-center justify-center overflow-hidden rounded-full bg-neutral-900 font-medium text-xs tracking-wider text-gray-50 cursor-pointer transition active:scale-95">
      <x-icons.close />
    </button>
    @elseif($panel->isPreviewMode())
    <button wire:click="enableThumbMode" class="group relative inline-flex h-7 w-7 items-center justify-center overflow-hidden rounded-full bg-neutral-900 font-medium text-xs tracking-wider text-gray-50 cursor-pointer transition active:scale-95">
      <x-icons.close />
    </button>
    @elseif($panel->isEditorMode())
    <button wire:click="closeImageEditor" class="group relative inline-flex h-7 w-7 items-center justify-center overflow-hidden rounded-full bg-neutral-900 font-medium text-xs tracking-wider text-gray-50 cursor-pointer transition active:scale-95">
      <x-icons.close />
    </button>
    @endif

    {{-- Thumb Mode Controls --}}
    @if($panel->isThumbMode())

    @if($show->isShowOrderBy() && $this->paginator->isNotEmpty())
    <x-mediable::toolbar-select wire:model.live="orderBy" :options="$orderColumns" />
    @endif

    @if($show->isShowOrderDir() && $this->paginator->isNotEmpty())
    <button type="button" class="relative flex items-center justify-between w-20 min-w-20 max-w-20 px-3 py-1.5 bg-neutral-900 rounded-full font-medium text-xs tracking-wider text-gray-50 cursor-pointer transition-all duration-100 ease-in" wire:click="toggleOrderDir()">
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

    @if($show->isShowColumnWidth() && $this->paginator->isNotEmpty())
    <x-mediable::toolbar-select wire:model.live="defaultColumnWidth" :options="array_reverse($columnWidths, true)" />
    @endif

    @if($show->isShowUniqueMimeTypes() && count($this->uniqueMimeTypes) >= 1)
    <x-mediable::toolbar-select wire:model.live="selectedMimeType" placeholder="Mimes" :options="array_combine($this->uniqueMimeTypes, $this->uniqueMimeTypes)" />
    @endif

    @endif

    {{-- Loading Spinner (preview, editor modes) --}}
    @if($panel->isPreviewMode() || $panel->isEditorMode())
    <div wire:loading class="hidden">
      <div class="border-gray-300 h-6 w-6 animate-spin rounded-full border-2 border-t-blue-600"></div>
    </div>
    @endif

  </div>

  {{-- Right Section --}}
  <div class="flex items-center justify-end gap-1.5 xl:gap-2">

    {{-- Trash Button (preview mode only) --}}
    @if($panel->isPreviewMode() && $activeId)
    <button wire:click="deleteAttachment({{ $activeId }})" class="group relative inline-flex items-center justify-center overflow-hidden rounded-full bg-neutral-900 py-1.5 px-4 font-medium text-xs tracking-wider text-gray-50 cursor-pointer">
      <span class="absolute h-0 w-0 rounded-full bg-red-500 transition-all duration-300 group-hover:h-56 group-hover:w-32"></span>
      <span class="relative">Trash</span>
    </button>
    @endif

    {{-- Editor Button (thumb, preview modes) --}}
    @if(($panel->isThumbMode() || $panel->isPreviewMode()) && $show->isShowEditor() && $activeId)
    <button wire:click="enableEditorMode()" class="group relative inline-flex h-7 items-center justify-center overflow-hidden rounded-full bg-neutral-900 px-4 font-medium text-xs tracking-wider text-gray-50 cursor-pointer"><span>Editor</span>
      <div class="w-0 translate-x-full pl-0 opacity-0 transition-all duration-200 group-hover:w-5 group-hover:translate-x-0 group-hover:pl-1 group-hover:opacity-100"><svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5">
          <path d="M8.14645 3.14645C8.34171 2.95118 8.65829 2.95118 8.85355 3.14645L12.8536 7.14645C13.0488 7.34171 13.0488 7.65829 12.8536 7.85355L8.85355 11.8536C8.65829 12.0488 8.34171 12.0488 8.14645 11.8536C7.95118 11.6583 7.95118 11.3417 8.14645 11.1464L11.2929 8H2.5C2.22386 8 2 7.77614 2 7.5C2 7.22386 2.22386 7 2.5 7H11.2929L8.14645 3.85355C7.95118 3.65829 7.95118 3.34171 8.14645 3.14645Z" fill="currentColor" fill-rule="evenodd" clip-rule="evenodd"></path>
        </svg></div>
    </button>
    @endif

    {{-- Uploads Button (thumb mode only) --}}
    @if($panel->isThumbMode() && $show->isShowUpload())
    <button wire:click="enableUploadMode" class="group relative inline-flex h-7 items-center justify-center rounded-full bg-neutral-900 py-1.5 pl-5 pr-7 font-medium text-xs tracking-wider text-gray-50 cursor-pointer"><span class="z-10 pr-2">Uploads</span>
      <div class="absolute right-1 inline-flex h-6 w-6 items-center justify-end rounded-full bg-neutral-700 transition-[width] group-hover:w-[calc(100%-8px)]">
        <div class="mr-0.5 flex items-center justify-center"><svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-50">
            <path d="M8.14645 3.14645C8.34171 2.95118 8.65829 2.95118 8.85355 3.14645L12.8536 7.14645C13.0488 7.34171 13.0488 7.65829 12.8536 7.85355L8.85355 11.8536C8.65829 12.0488 8.34171 12.0488 8.14645 11.8536C7.95118 11.6583 7.95118 11.3417 8.14645 11.1464L11.2929 8H2.5C2.22386 8 2 7.77614 2 7.5C2 7.22386 2.22386 7 2.5 7H11.2929L8.14645 3.85355C7.95118 3.65829 7.95118 3.34171 8.14645 3.14645Z" fill="currentColor" fill-rule="evenodd" clip-rule="evenodd"></path>
          </svg></div>
      </div>
    </button>
    @endif

    {{-- Sidebar Toggle (thumb, preview, editor modes) --}}
    @if(!$panel->isUploadMode())
    <button type="button" class="group relative inline-flex h-7 w-7 items-center justify-center overflow-hidden rounded-full bg-neutral-900 font-medium text-xs tracking-wider text-gray-50 cursor-pointer" wire:click="toggleSidebar">
      <x-icons.arrow :direction="$show->isShowSidebar() ? 'right' : 'left'" />
    </button>
    @endif

  </div>

</div>
