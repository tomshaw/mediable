<div class="flex items-center justify-between h-full w-full px-4">

  @if($panel->isUploadMode())
  <div class="flex items-center justify-start gap-2">
    <button wire:click="enableThumbMode" class="group relative inline-flex h-7 w-7 items-center justify-center overflow-hidden rounded-full bg-[#555] font-medium text-neutral-200 transition active:scale-95">
      <x-icons.close />
    </button>
  </div>
  @if(count($files) >= 1)
  <div class="flex items-center justify-end gap-2">
    <button wire:click="clearFiles" class="group relative inline-flex items-center justify-center overflow-hidden rounded-full bg-[#555] py-1.5 px-4 text-xs font-normal text-white">
      <span class="absolute h-0 w-0 rounded-full bg-red-500 transition-all duration-300 group-hover:h-56 group-hover:w-32"></span>
      <span class="relative">Reset</span>
    </button>
    <button type="button" class="group relative inline-flex items-center justify-center overflow-hidden rounded-full bg-[#555] w-full py-1.5 px-4 font-normal text-xs text-neutral-50" wire:click="createAttachments" wire:loading.attr="disabled">
      <span class="absolute h-0 w-0 rounded-full bg-blue-400 transition-all duration-300 group-hover:h-full group-hover:w-full"></span>
      <span class="relative" wire:loading.remove wire:target="createAttachments">Submit Attachments</span>
      <span class="relative" wire:loading wire:target="createAttachments">Processing...</span>
    </button>
  </div>
  @endif
  @endif

  @if($panel->isThumbMode())
  <div class="flex items-center justify-start gap-2">

    <button type="button" class="group relative inline-flex h-7 w-7 items-center justify-center overflow-hidden rounded-full bg-[#555] font-medium text-neutral-200" wire:click="toggleMetaInfo">
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
    <button type="button" class="relative flex items-center justify-between w-[80px] min-w-[80px] max-w-[80px] px-3 py-1.5 bg-[#555] text-white rounded-full text-xs font-normal cursor-pointer transition-all duration-100 ease-in" wire:click="toggleOrderDir()">
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
  <div class="flex items-center justify-end gap-2">

    @if ($show->isShowEditor() && count($selected))
    <button wire:click="enableEditorMode()" class="group relative inline-flex h-7 items-center justify-center overflow-hidden rounded-full bg-[#555] px-4 text-xs font-normal text-white"><span>Editor</span>
      <div class="w-0 translate-x-[100%] pl-0 opacity-0 transition-all duration-200 group-hover:w-5 group-hover:translate-x-0 group-hover:pl-1 group-hover:opacity-100"><svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5">
          <path d="M8.14645 3.14645C8.34171 2.95118 8.65829 2.95118 8.85355 3.14645L12.8536 7.14645C13.0488 7.34171 13.0488 7.65829 12.8536 7.85355L8.85355 11.8536C8.65829 12.0488 8.34171 12.0488 8.14645 11.8536C7.95118 11.6583 7.95118 11.3417 8.14645 11.1464L11.2929 8H2.5C2.22386 8 2 7.77614 2 7.5C2 7.22386 2.22386 7 2.5 7H11.2929L8.14645 3.85355C7.95118 3.65829 7.95118 3.34171 8.14645 3.14645Z" fill="currentColor" fill-rule="evenodd" clip-rule="evenodd"></path>
        </svg></div>
    </button>
    @endif

    @if ($show->isShowPreview() && count($selected))
    <button wire:click="enablePreviewMode()" class="group relative inline-flex h-7 items-center justify-center overflow-hidden rounded-full bg-[#555] px-4 text-xs font-normal text-white"><span>Preview</span>
      <div class="w-0 translate-x-[100%] pl-0 opacity-0 transition-all duration-200 group-hover:w-5 group-hover:translate-x-0 group-hover:pl-1 group-hover:opacity-100"><svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5">
          <path d="M8.14645 3.14645C8.34171 2.95118 8.65829 2.95118 8.85355 3.14645L12.8536 7.14645C13.0488 7.34171 13.0488 7.65829 12.8536 7.85355L8.85355 11.8536C8.65829 12.0488 8.34171 12.0488 8.14645 11.8536C7.95118 11.6583 7.95118 11.3417 8.14645 11.1464L11.2929 8H2.5C2.22386 8 2 7.77614 2 7.5C2 7.22386 2.22386 7 2.5 7H11.2929L8.14645 3.85355C7.95118 3.65829 7.95118 3.34171 8.14645 3.14645Z" fill="currentColor" fill-rule="evenodd" clip-rule="evenodd"></path>
        </svg></div>
    </button>
    @endif

    @if ($show->isShowUpload())
    <button wire:click="enableUploadMode" class="group relative inline-flex h-7 items-center justify-center rounded-full bg-[#555] py-1.5 pl-5 pr-7 font-normal text-xs text-neutral-50"><span class="z-10 pr-2">Uploads</span>
      <div class="absolute right-1 inline-flex h-6 w-6 items-center justify-end rounded-full bg-[#696969] transition-[width] group-hover:w-[calc(100%-8px)]">
        <div class="mr-[2px] flex items-center justify-center"><svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-neutral-50">
            <path d="M8.14645 3.14645C8.34171 2.95118 8.65829 2.95118 8.85355 3.14645L12.8536 7.14645C13.0488 7.34171 13.0488 7.65829 12.8536 7.85355L8.85355 11.8536C8.65829 12.0488 8.34171 12.0488 8.14645 11.8536C7.95118 11.6583 7.95118 11.3417 8.14645 11.1464L11.2929 8H2.5C2.22386 8 2 7.77614 2 7.5C2 7.22386 2.22386 7 2.5 7H11.2929L8.14645 3.85355C7.95118 3.65829 7.95118 3.34171 8.14645 3.14645Z" fill="currentColor" fill-rule="evenodd" clip-rule="evenodd"></path>
          </svg></div>
      </div>
    </button>
    @endif

    <button type="button" class="group relative inline-flex h-7 w-7 items-center justify-center overflow-hidden rounded-full bg-[#555] font-medium text-neutral-200" wire:click="toggleSidebar">
      <x-icons.arrow :show="$show->isShowSidebar()" direction="left" />
    </button>
  </div>
  @endif

  @if($panel->isPreviewMode())
  <div class="flex items-center justify-start gap-2">

    <button type="button" class="group relative inline-flex h-7 w-7 items-center justify-center overflow-hidden rounded-full bg-[#555] font-medium text-neutral-200" wire:click="toggleMetaInfo">
      <x-icons.arrow :show="$show->isShowMetaInfo()" direction="right" />
    </button>

    <button wire:click="enableThumbMode" class="group relative inline-flex h-7 w-7 items-center justify-center overflow-hidden rounded-full bg-[#555] font-medium text-neutral-200 transition active:scale-95">
      <x-icons.close />
    </button>

    <div wire:loading class="hidden">
      <div class="border-gray-300 h-6 w-6 animate-spin rounded-full border-2 border-t-blue-600"></div>
    </div>

  </div>
  <div class="flex flex-row items-center justify-end gap-2">

    <button wire:click="deleteAttachment({{$attachment->id}})" class="group relative inline-flex items-center justify-center overflow-hidden rounded-full bg-[#555] py-1.5 px-4 text-xs font-normal text-white">
      <span class="absolute h-0 w-0 rounded-full bg-red-500 transition-all duration-300 group-hover:h-56 group-hover:w-32"></span>
      <span class="relative">Trash</span>
    </button>

    @if ($show->isShowEditor() && count($selected))
    <button wire:click="enableEditorMode()" class="group relative inline-flex h-7 items-center justify-center overflow-hidden rounded-full bg-[#555] px-4 text-xs font-normal text-white"><span>Editor</span>
      <div class="w-0 translate-x-[100%] pl-0 opacity-0 transition-all duration-200 group-hover:w-5 group-hover:translate-x-0 group-hover:pl-1 group-hover:opacity-100"><svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5">
          <path d="M8.14645 3.14645C8.34171 2.95118 8.65829 2.95118 8.85355 3.14645L12.8536 7.14645C13.0488 7.34171 13.0488 7.65829 12.8536 7.85355L8.85355 11.8536C8.65829 12.0488 8.34171 12.0488 8.14645 11.8536C7.95118 11.6583 7.95118 11.3417 8.14645 11.1464L11.2929 8H2.5C2.22386 8 2 7.77614 2 7.5C2 7.22386 2.22386 7 2.5 7H11.2929L8.14645 3.85355C7.95118 3.65829 7.95118 3.34171 8.14645 3.14645Z" fill="currentColor" fill-rule="evenodd" clip-rule="evenodd"></path>
        </svg></div>
    </button>
    @endif

    <button type="button" class="group relative inline-flex h-7 w-7 items-center justify-center overflow-hidden rounded-full bg-[#555] font-medium text-neutral-200" wire:click="toggleSidebar">
      <x-icons.arrow :show="$show->isShowSidebar()" direction="left" />
    </button>
  </div>
  @endif

  @if($panel->isEditorMode())
  <div class="flex items-center justify-start gap-2">
    <button type="button" class="group relative inline-flex h-7 w-7 items-center justify-center overflow-hidden rounded-full bg-[#555] font-medium text-neutral-200" wire:click="toggleMetaInfo">
      <x-icons.arrow :show="$show->isShowMetaInfo()" direction="right" />
    </button>

    <button wire:click="closeImageEditor" class="group relative inline-flex h-7 w-7 items-center justify-center overflow-hidden rounded-full bg-[#555] font-medium text-neutral-200 transition active:scale-95">
      <x-icons.close />
    </button>

    <div wire:loading class="hidden">
      <div class="border-gray-300 h-6 w-6 animate-spin rounded-full border-2 border-t-blue-600"></div>
    </div>
  </div>
  <div class="flex flex-row items-center justify-end gap-2">
    @if(count($editHistory))
    <button type="button" wire:click="undoEditorChanges" class="relative flex items-center justify-center px-4 py-1.5 gap-x-2 bg-[#555] text-white rounded-full text-xs font-normal cursor-pointer transition-all duration-100 ease-in">Undo</button>
    <button type="button" wire:click="saveEditorChanges" class="relative flex items-center justify-center px-4 py-1.5 gap-x-2 bg-[#555] text-white rounded-full text-xs font-normal cursor-pointer transition-all duration-100 ease-in">Save</button>
    @endif
    <button type="button" class="group relative inline-flex h-7 w-7 items-center justify-center overflow-hidden rounded-full bg-[#555] font-medium text-neutral-200" wire:click="toggleSidebar">
      <x-icons.arrow :show="$show->isShowSidebar()" direction="left" />
    </button>
  </div>
  @endif

</div>