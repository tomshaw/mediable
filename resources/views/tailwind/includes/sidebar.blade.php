<div class="absolute inset-0 p-0 m-0 w-full">

  <div class="absolute top-0 bg-[#E6E6E6] border-b border-[#ccc] w-full h-[50px] min-h-[50px]">
    <div class="flex items-center justify-between px-4 h-full w-full">
      <h3 class="text-[#666] text-sm font-bold uppercase">File Options</h3>
      <button type="button" wire:click="toggleSidebar">
        <x-icons.expand />
      </button>
    </div>
  </div>

  <div class="p-4 my-[50px] h-auto">
    <form wire:submit.prevent="updateAttachment" role="form">
      <div class="p-0 m-0">
        <div class="mb-2">
          <label class="inline-block text-gray-500 mb-1 text-xs font-normal">Title</label>
          <input type="text" class="control-input" wire:model="title" spellcheck="false">
        </div>
        <div class="mb-2">
          <label class="inline-block text-gray-500 mb-1 text-xs font-normal">Caption</label>
          <input type="text" class="control-input" wire:model="caption" spellcheck="false">
        </div>
        <div class="mb-2">
          <label class="inline-block text-gray-500 mb-1 text-xs font-normal">Description</label>
          <textarea class="control-input focus:ring-0" wire:model="description" rows="4" spellcheck="false"></textarea>
        </div>
      </div>
      <div class="flex items-center justify-between">
        <button type="button" class="btn" wire:click="deleteAttachment({{$modelId}})">Delete</button>
        <div>
          <button type="reset" class="btn">Reset</button>
          <button type="submit" class="btn">Submit</button>
        </div>
      </div>
    </form>
  </div>

  <div class="absolute bottom-0 bg-[#E6E6E6] border-t border-[#ccc] w-full h-[50px] min-h-[50px]">
    <div class="flex items-center justify-start px-4 h-full w-full gap-x-2">
      <div wire:loading class="hidden">
        <div class="border-gray-300 h-6 w-6 animate-spin rounded-full border-2 border-t-blue-600"></div>
      </div>
    </div>
  </div>
</div>