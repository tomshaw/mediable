<div class="flex items-center justify-between h-full w-full px-4">

  @if($panel->isUploadMode())
  <div class="flex items-center justify-start gap-2">
    <button type="button" wire:click="enableThumbMode" class="relative flex items-center justify-center px-4 py-1.5 gap-x-2 bg-[#555] text-white rounded-full text-xs font-normal cursor-pointer transition-all duration-100 ease-in">Close</button>
  </div>
  @if(count($files) >= 1)
  <div class="flex items-center justify-end gap-2">
    <button type="button" class="relative flex items-center justify-center px-4 py-1.5 gap-x-2 bg-[#555] text-white rounded-full text-xs font-normal cursor-pointer transition-all duration-100 ease-in" wire:click="clearFiles()">Reset</button>
    <button type="button" class="relative flex items-center justify-center px-4 py-1.5 gap-x-2 bg-[#555] text-white rounded-full text-xs font-normal cursor-pointer transition-all duration-100 ease-in" wire:click="createAttachments()" wire:loading.attr="disabled">
      <span wire:loading.remove>Add Attachments</span>
      <span wire:loading>Processing...</span>
    </button>
  </div>
  @endif
  @endif

  @if($panel->isThumbMode())
  <div class="flex items-center justify-start gap-2">

    @if($show->isShowOrderBy())
    <select class="control-select" wire:model.live="orderBy">
      @foreach($orderColumns as $key => $value)
      <option value="{{$key}}">{{ $value }}</option>
      @endforeach
    </select>
    @endif

    @if($show->isShowOrderDir())
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

    @if($show->isShowColumnWidth())
    <select class="control-select" wire:model.live="defaultColumnWidth">
      @foreach(array_reverse($columnWidths, true) as $key => $value)
      <option value="{{$key}}">{{ $value }}</option>
      @endforeach
    </select>
    @endif

    @if($show->isShowUniqueMimeTypes() && count($uniqueMimeTypes)>=1)
    <select class="control-select" wire:model.live="selectedMimeType">
      <option value="">Mime Types</option>
      @foreach($uniqueMimeTypes as $mimeType)
      <option value="{{$mimeType}}">{{ $mimeType }}</option>
      @endforeach
    </select>
    @endif

  </div>
  <div class="flex items-center justify-end gap-2">
    @if ($show->isShowEditor() && count($selected))
    <button type="button" wire:click="enableEditorMode()" class="relative flex items-center justify-center px-4 py-1.5 gap-x-2 bg-[#555] text-white rounded-full text-xs font-normal cursor-pointer transition-all duration-100 ease-in">Editor</button>
    @endif

    @if ($show->isShowPreview() && count($selected))
    <button type="button" wire:click="enablePreviewMode()" class="relative flex items-center justify-center px-4 py-1.5 gap-x-2 bg-[#555] text-white rounded-full text-xs font-normal cursor-pointer transition-all duration-100 ease-in">Preview</button>
    @endif

    @if ($show->isShowUpload())
    <button type="button" wire:click="enableUploadMode" class="relative flex items-center justify-center px-4 py-1.5 gap-x-2 bg-[#555] text-white rounded-full text-xs font-normal cursor-pointer transition-all duration-100 ease-in">Uploads</button>
    @endif
  </div>
  @endif

  @if($panel->isPreviewMode())
  <div class="flex items-center justify-start gap-2">
    <button type="button" wire:click="enableThumbMode" class="relative flex items-center justify-center px-4 py-1.5 gap-x-2 bg-[#555] text-white rounded-full text-xs font-normal cursor-pointer transition-all duration-100 ease-in">Close</button>
    <div wire:loading class="hidden">
      <div class="border-gray-300 h-6 w-6 animate-spin rounded-full border-2 border-t-blue-600"></div>
    </div>
  </div>
  <div class="flex flex-row items-center justify-end gap-4">
    @if ($show->isShowEditor() && count($selected))
    <button type="button" wire:click="enableEditorMode()" class="relative flex items-center justify-center px-4 py-1.5 gap-x-2 bg-[#555] text-white rounded-full text-xs font-normal cursor-pointer transition-all duration-100 ease-in">Editor</button>
    @endif
  </div>
  @endif

  @if($panel->isEditorMode())
  <div class="flex items-center justify-start gap-2">
    <div wire:loading class="hidden">
      <div class="border-gray-300 h-6 w-6 animate-spin rounded-full border-2 border-t-blue-600"></div>
    </div>
  </div>
  <div class="flex flex-row items-center justify-end gap-4">
    <button type="button" wire:click="enableThumbMode" class="relative flex items-center justify-center px-4 py-1.5 gap-x-2 bg-[#555] text-white rounded-full text-xs font-normal cursor-pointer transition-all duration-100 ease-in">Close</button>
  </div>
  @endif

</div>