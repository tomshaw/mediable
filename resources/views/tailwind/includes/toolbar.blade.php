<div class="flex items-center justify-between bg-[#E6E6E6] p-0 m-0 px-4 w-full h-[50px] min-h-[50px] border-b border-[#ccc]">

  @if($uploadMode)
  <div class="flex items-center justify-start gap-2">
    <button type="button" wire:click="enableThumbMode" class="relative flex items-center justify-center px-4 py-1.5 gap-x-2 bg-[#555] text-white rounded-full text-xs font-normal cursor-pointer transition-all duration-100 ease-in">Close</button>
  </div>
  <div class="flex items-center justify-end gap-2">
    <button type="button" class="relative flex items-center justify-center px-4 py-1.5 gap-x-2 bg-[#555] text-white rounded-full text-xs font-normal cursor-pointer transition-all duration-100 ease-in" wire:click="clearFiles()">Reset</button>
    <button type="button" class="relative flex items-center justify-center px-4 py-1.5 gap-x-2 bg-[#555] text-white rounded-full text-xs font-normal cursor-pointer transition-all duration-100 ease-in" wire:click="createAttachments()">Submit</button>
  </div>
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

    @if($showColumnWidth && !$tableMode)
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
    <button type="button" wire:click="enableTableMode"><x-icons.tables /></button>
    <button type="button" wire:click="enableUploadMode" class="relative flex items-center justify-center px-4 py-1.5 gap-x-2 bg-[#555] text-white rounded-full text-xs font-normal cursor-pointer transition-all duration-100 ease-in">File Uploads</button>
  </div>
  @endif

  @if($tableMode)
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

    @if($showUniqueMimeTypes)
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
    <button type="button" wire:click="enableThumbMode"><x-icons.attachments /></button>
    <button type="button" wire:click="enableUploadMode" class="relative flex items-center justify-center px-4 py-1.5 gap-x-2 bg-[#555] text-white rounded-full text-xs font-normal cursor-pointer transition-all duration-100 ease-in">File Uploads</button>
  </div>
  @endif

</div>