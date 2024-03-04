<div class="flex items-center justify-between bg-[#E6E6E6] p-0 m-0 px-4 w-full h-[50px] min-h-[50px] border-b border-[#ccc]">

  @if($uploadMode)
  <div class="flex items-center justify-start gap-2">
    <button type="button" wire:click="enableThumbMode" class="relative flex items-center justify-center px-4 py-1.5 gap-x-2 bg-[#555] text-white rounded-full text-xs font-normal cursor-pointer transition-all duration-100 ease-in">Close</button>
  </div>
  @if(count($files) >= 1)
  <div class="flex items-center justify-end gap-2">
    <button type="button" class="relative flex items-center justify-center px-4 py-1.5 gap-x-2 bg-[#555] text-white rounded-full text-xs font-normal cursor-pointer transition-all duration-100 ease-in" wire:click="clearFiles()">Reset</button>
    <button type="button" class="relative flex items-center justify-center px-4 py-1.5 gap-x-2 bg-[#555] text-white rounded-full text-xs font-normal cursor-pointer transition-all duration-100 ease-in" wire:click="createAttachments()" wire:loading.attr="disabled">
      <span wire:loading.remove wire:target="createAttachments">Add Attachments</span>
      <span wire:loading wire:target="createAttachments">Processing...</span>
    </button>
  </div>
  @endif
  @endif

  @if($thumbMode)
  <div class="flex items-center justify-start gap-2">

    @if($showOrderBy)
    <select class="control-select" wire:model.live="orderBy">
      @foreach($orderColumns as $key => $value)
      <option value="{{$key}}">{{ $value }}</option>
      @endforeach
    </select>
    @endif

    @if($showOrderDir)
    <select class="control-select" wire:model.live="orderDir">
      @foreach($orderDirValues as $key => $value)
      <option value="{{$key}}"> @if($key == 'ASC') Ascending @else Descending @endif</option>
      @endforeach
    </select>
    @endif

    @if($showColumnWidth)
    <select class="control-select" wire:model.live="defaultColumnWidth">
      @foreach(array_reverse($columnWidths, true) as $key => $value)
      <option value="{{$key}}">{{ $value }}</option>
      @endforeach
    </select>
    @endif

    @if($showUniqueMimeTypes && count($uniqueMimeTypes)>=1)
    <select class="control-select" wire:model.live="selectedMimeType">
      <option value="">Mime Types</option>
      @foreach($uniqueMimeTypes as $mimeType)
      <option value="{{$mimeType}}">{{ $mimeType }}</option>
      @endforeach
    </select>
    @endif

    <div wire:loading class="hidden">
      <div class="border-gray-300 h-6 w-6 animate-spin rounded-full border-2 border-t-blue-600"></div>
    </div>

  </div>
  <div class="flex items-center justify-end gap-2">
    <button type="button" wire:click="enableUploadMode" class="relative flex items-center justify-center px-4 py-1.5 gap-x-2 bg-[#555] text-white rounded-full text-xs font-normal cursor-pointer transition-all duration-100 ease-in">File Uploads</button>
  </div>
  @endif

  @if($previewMode)
  <div class="flex items-center justify-start gap-2">

    <select class="control-select" wire:model="flipMode" wire:change="flipImage">
      <option value="">Flip Modes</option>
      @foreach($this->getFlipModes() as $key => $value)
      <option value="{{ $key }}">{{ $value }}</option>
      @endforeach
    </select>

    <select class="control-select" wire:model="filterMode" wire:change="filterImage">
      <option value="">Filter Modes</option>
      @foreach($this->getFilterModes() as $key => $value)
      <option value="{{ $key }}">{{ $value }}</option>
      @endforeach
    </select>

    <div class="flex flex-row flex-wrap items-center space-x-4">
      @if($filterMode == IMG_FILTER_CONTRAST)
      <label class="text-sm text-gray-500 flex items-center space-x-2 whitespace-nowrap">
        <span>Contrast:</span>
        <input type="number" class="control-input" wire:model="contrast" min="-100" max="100" step="1" />
      </label>
      @endif

      @if($filterMode == IMG_FILTER_BRIGHTNESS)
      <label class="text-sm text-gray-500 flex items-center space-x-2 whitespace-nowrap">
        <span>Brightness:</span>
        <input type="number" class="control-input" wire:model="brightness" min="-255" max="255" step="1" />
      </label>
      @endif

      @if($filterMode == IMG_FILTER_COLORIZE)
      <label class="text-sm text-gray-500 flex items-center space-x-2 whitespace-nowrap">
        <span>Red:</span>
        <input type="number" class="control-input" wire:model="colorizeRed" min="-255" max="255" step="1" />
      </label>
      <label class="text-sm text-gray-500 flex items-center space-x-2 whitespace-nowrap">
        <span>Green:</span>
        <input type="number" class="control-input" wire:model="colorizeGreen" min="-255" max="255" step="1" />
      </label>
      <label class="text-sm text-gray-500 flex items-center space-x-2 whitespace-nowrap">
        <span>Blue:</span>
        <input type="number" class="control-input" wire:model="colorizeBlue" min="-255" max="255" step="1" />
      </label>
      @endif

      @if($filterMode == IMG_FILTER_SMOOTH)
      <label class="text-sm text-gray-500 flex items-center space-x-2 whitespace-nowrap">
        <span>Smooth Level:</span>
        <input type="number" class="control-input" wire:model="smoothLevel" min="-10" max="10" step="1" />
      </label>
      @endif

      @if($filterMode == IMG_FILTER_PIXELATE)
      <label class="text-sm text-gray-500 flex items-center space-x-2 whitespace-nowrap">
        <span>Pixelate Block Size:</span>
        <input type="number" class="control-input" wire:model="pixelateBlockSize" min="1" step="1" />
      </label>
      @endif

      <button class="relative flex items-center justify-center px-4 py-1.5 gap-x-2 bg-[#555] text-white rounded-full text-xs font-normal cursor-pointer transition-all duration-100 ease-in" wire:click="filterImage">Go</button>
    </div>

    <div wire:loading class="hidden">
      <div class="border-gray-300 h-6 w-6 animate-spin rounded-full border-2 border-t-blue-600"></div>
    </div>

  </div>
  <div class="flex flex-row items-center justify-end gap-4">

    @if ($this->mimeTypeImage($this->fileType))
    <div class="flex flex-row flex-wrap items-center space-x-4">
      <div class="mt-1.5">
        <input type="range" min="1" max="100" value="{{ $this->imageWidth }}" wire:model.live="imageWidth">
      </div>
    </div>
    @endif

    <button type="button" wire:click="enableThumbMode" class="relative flex items-center justify-center px-4 py-1.5 gap-x-2 bg-[#555] text-white rounded-full text-xs font-normal cursor-pointer transition-all duration-100 ease-in">Close</button>
  </div>
  @endif
</div>