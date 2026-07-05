<div class="flex items-center justify-between h-full w-full gap-2 px-3">

  {{-- Left Section --}}
  <div class="flex items-center justify-start gap-1.5 min-w-0">

    {{-- Meta Toggle (thumb, preview, editor modes) --}}
    @if(!$panel->isUploadMode())
    <button type="button" @class([
      'inline-flex items-center justify-center h-8 w-8 rounded-lg cursor-pointer transition-colors',
      $show->isShowMetaInfo()
        ? 'text-zinc-900 bg-zinc-100 dark:text-zinc-100 dark:bg-zinc-800'
        : 'text-zinc-400 hover:text-zinc-900 hover:bg-zinc-100 dark:text-zinc-500 dark:hover:text-zinc-100 dark:hover:bg-zinc-800'
    ]) wire:click="toggleMetaInfo" title="{{ $show->isShowMetaInfo() ? 'Hide info panel' : 'Show info panel' }}">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-4.5 h-4.5">
        <rect x="3.25" y="4.75" width="17.5" height="14.5" rx="2.25" />
        <path d="M9.25 4.75v14.5" />
      </svg>
    </button>
    @endif

    {{-- Close/Back Button --}}
    @if($panel->isUploadMode() && !$this->paginator->isEmpty())
    <button wire:click="enableThumbMode" class="inline-flex items-center gap-1 h-8 rounded-lg pl-1.5 pr-2.5 text-xs font-medium text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:text-zinc-100 dark:hover:bg-zinc-800 cursor-pointer transition-colors">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
        <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 0 1-.02 1.06L8.832 10l3.938 3.71a.75.75 0 1 1-1.04 1.08l-4.5-4.25a.75.75 0 0 1 0-1.08l4.5-4.25a.75.75 0 0 1 1.06.02Z" clip-rule="evenodd" />
      </svg>
      Library
    </button>
    @elseif($panel->isPreviewMode())
    <button wire:click="enableThumbMode" class="inline-flex items-center gap-1 h-8 rounded-lg pl-1.5 pr-2.5 text-xs font-medium text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:text-zinc-100 dark:hover:bg-zinc-800 cursor-pointer transition-colors">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
        <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 0 1-.02 1.06L8.832 10l3.938 3.71a.75.75 0 1 1-1.04 1.08l-4.5-4.25a.75.75 0 0 1 0-1.08l4.5-4.25a.75.75 0 0 1 1.06.02Z" clip-rule="evenodd" />
      </svg>
      Library
    </button>
    @elseif($panel->isEditorMode())
    <button wire:click="closeImageEditor" class="inline-flex items-center gap-1 h-8 rounded-lg pl-1.5 pr-2.5 text-xs font-medium text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:text-zinc-100 dark:hover:bg-zinc-800 cursor-pointer transition-colors">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
        <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 0 1-.02 1.06L8.832 10l3.938 3.71a.75.75 0 1 1-1.04 1.08l-4.5-4.25a.75.75 0 0 1 0-1.08l4.5-4.25a.75.75 0 0 1 1.06.02Z" clip-rule="evenodd" />
      </svg>
      Library
    </button>
    @endif

    {{-- Panel Title (preview, editor, upload modes) --}}
    @if($panel->isPreviewMode())
    <span class="hidden md:inline text-[11px] font-medium uppercase tracking-widest text-zinc-400 dark:text-zinc-500 select-none pl-1">Preview</span>
    @elseif($panel->isEditorMode())
    <span class="hidden md:inline text-[11px] font-medium uppercase tracking-widest text-zinc-400 dark:text-zinc-500 select-none pl-1">Image editor</span>
    @elseif($panel->isUploadMode())
    <span class="hidden md:inline text-[11px] font-medium uppercase tracking-widest text-zinc-400 dark:text-zinc-500 select-none pl-1">Upload</span>
    @endif

    {{-- Thumb Mode Controls --}}
    @if($panel->isThumbMode())

    @if($show->isShowOrderBy() && $this->paginator->isNotEmpty())
    <x-mediable::toolbar-select wire:model.live="orderBy" :options="$orderColumns" title="Sort by" />
    @endif

    @if($show->isShowOrderDir() && $this->paginator->isNotEmpty())
    <button type="button" class="inline-flex items-center justify-center gap-1 h-8 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-2 text-xs font-medium text-zinc-600 dark:text-zinc-300 hover:border-zinc-300 dark:hover:border-zinc-600 cursor-pointer transition-colors" wire:click="toggleOrderDir()" title="{{ strtoupper($orderDir) === 'ASC' ? 'Ascending — click for descending' : 'Descending — click for ascending' }}">
      @if(strtoupper($orderDir) == 'ASC')
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
        <path fill-rule="evenodd" d="M10 17a.75.75 0 0 1-.75-.75V5.612L5.29 9.77a.75.75 0 0 1-1.08-1.04l5.25-5.5a.75.75 0 0 1 1.08 0l5.25 5.5a.75.75 0 1 1-1.08 1.04l-3.96-4.158V16.25A.75.75 0 0 1 10 17Z" clip-rule="evenodd" />
      </svg>
      @else
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
        <path fill-rule="evenodd" d="M10 3a.75.75 0 0 1 .75.75v10.638l3.96-4.158a.75.75 0 1 1 1.08 1.04l-5.25 5.5a.75.75 0 0 1-1.08 0l-5.25-5.5a.75.75 0 1 1 1.08-1.04l3.96 4.158V3.75A.75.75 0 0 1 10 3Z" clip-rule="evenodd" />
      </svg>
      @endif
      <span class="hidden xl:inline">{{ strtoupper($orderDir) === 'ASC' ? 'Asc' : 'Desc' }}</span>
    </button>
    @endif

    @if($show->isShowColumnWidth() && $this->paginator->isNotEmpty())
    <x-mediable::toolbar-select wire:model.live="defaultColumnWidth" :options="collect($columnWidths)->mapWithKeys(fn ($width, $index) => [$index => intval(round(100 / $width)).' per row'])->all()" title="Grid size" />
    @endif

    @if($show->isShowUniqueMimeTypes() && count($this->uniqueMimeTypes) >= 1)
    <x-mediable::toolbar-select wire:model.live="selectedMimeType" placeholder="All types" :options="array_combine($this->uniqueMimeTypes, $this->uniqueMimeTypes)" title="Filter by type" />
    @endif

    @endif

    {{-- Loading Spinner (preview, editor modes) --}}
    @if($panel->isPreviewMode() || $panel->isEditorMode())
    <div wire:loading class="hidden">
      <div class="border-zinc-300 dark:border-zinc-700 h-5 w-5 animate-spin rounded-full border-2 border-t-indigo-500"></div>
    </div>
    @endif

  </div>

  {{-- Right Section --}}
  <div class="flex items-center justify-end gap-1.5 shrink-0">

    {{-- Trash Button (preview mode only) --}}
    @if($panel->isPreviewMode() && $activeId)
    <button wire:click="deleteAttachment({{ $activeId }})" class="inline-flex items-center gap-1.5 h-8 rounded-lg px-2.5 text-xs font-medium text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950/40 cursor-pointer transition-colors" title="Delete this attachment">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-3.5 h-3.5">
        <path fill-rule="evenodd" d="M5 3.25V4H2.75a.75.75 0 0 0 0 1.5h.3l.815 8.15A1.5 1.5 0 0 0 5.357 15h5.285a1.5 1.5 0 0 0 1.493-1.35l.815-8.15h.3a.75.75 0 0 0 0-1.5H11v-.75A2.25 2.25 0 0 0 8.75 1h-1.5A2.25 2.25 0 0 0 5 3.25Zm2.25-.75a.75.75 0 0 0-.75.75V4h3v-.75a.75.75 0 0 0-.75-.75h-1.5ZM6.05 6a.75.75 0 0 1 .787.713l.275 5.5a.75.75 0 0 1-1.498.075l-.275-5.5A.75.75 0 0 1 6.05 6Zm3.9 0a.75.75 0 0 1 .712.787l-.275 5.5a.75.75 0 0 1-1.498-.075l.275-5.5A.75.75 0 0 1 9.95 6Z" clip-rule="evenodd" />
      </svg>
      Delete
    </button>
    @endif

    {{-- Editor Button (thumb, preview modes) --}}
    @if(($panel->isThumbMode() || $panel->isPreviewMode()) && $show->isShowEditor() && $activeId)
    <button wire:click="enableEditorMode()" class="inline-flex items-center gap-1.5 h-8 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-2.5 text-xs font-medium text-zinc-700 dark:text-zinc-200 hover:border-zinc-300 dark:hover:border-zinc-600 cursor-pointer transition-colors" title="Edit image">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5">
        <path d="m5.433 13.917 1.262-3.155A4 4 0 0 1 7.58 9.42l6.92-6.918a2.121 2.121 0 0 1 3 3l-6.92 6.918c-.383.383-.84.685-1.343.886l-3.154 1.262a.5.5 0 0 1-.65-.65Z" />
        <path d="M3.5 5.75c0-.69.56-1.25 1.25-1.25H10A.75.75 0 0 0 10 3H4.75A2.75 2.75 0 0 0 2 5.75v9.5A2.75 2.75 0 0 0 4.75 18h9.5A2.75 2.75 0 0 0 17 15.25V10a.75.75 0 0 0-1.5 0v5.25c0 .69-.56 1.25-1.25 1.25h-9.5c-.69 0-1.25-.56-1.25-1.25v-9.5Z" />
      </svg>
      Edit
    </button>
    @endif

    {{-- Uploads Button (thumb mode only) --}}
    @if($panel->isThumbMode() && $show->isShowUpload())
    <button wire:click="enableUploadMode" class="inline-flex items-center gap-1.5 h-8 rounded-lg bg-zinc-900 dark:bg-zinc-100 px-3 text-xs font-medium text-white dark:text-zinc-900 hover:bg-zinc-700 dark:hover:bg-white cursor-pointer transition-colors" title="Upload files">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5">
        <path d="M9.25 13.25a.75.75 0 0 0 1.5 0V4.636l2.955 3.129a.75.75 0 0 0 1.09-1.03l-4.25-4.5a.75.75 0 0 0-1.09 0l-4.25 4.5a.75.75 0 1 0 1.09 1.03L9.25 4.636v8.614Z" />
        <path d="M3.5 12.75a.75.75 0 0 0-1.5 0v2.5A2.75 2.75 0 0 0 4.75 18h10.5A2.75 2.75 0 0 0 18 15.25v-2.5a.75.75 0 0 0-1.5 0v2.5c0 .69-.56 1.25-1.25 1.25H4.75c-.69 0-1.25-.56-1.25-1.25v-2.5Z" />
      </svg>
      Upload
    </button>
    @endif

    {{-- Sidebar Toggle (thumb, preview, editor modes) --}}
    @if(!$panel->isUploadMode())
    <button type="button" @class([
      'inline-flex items-center justify-center h-8 w-8 rounded-lg cursor-pointer transition-colors',
      $show->isShowSidebar()
        ? 'text-zinc-900 bg-zinc-100 dark:text-zinc-100 dark:bg-zinc-800'
        : 'text-zinc-400 hover:text-zinc-900 hover:bg-zinc-100 dark:text-zinc-500 dark:hover:text-zinc-100 dark:hover:bg-zinc-800'
    ]) wire:click="toggleSidebar" title="{{ $show->isShowSidebar() ? 'Hide details panel' : 'Show details panel' }}">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-4.5 h-4.5">
        <rect x="3.25" y="4.75" width="17.5" height="14.5" rx="2.25" />
        <path d="M14.75 4.75v14.5" />
      </svg>
    </button>
    @endif

  </div>

</div>
