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
          <input type="text" class="control-input" wire:model="model.title" spellcheck="false">
        </div>
        <div class="mb-2">
          <label class="inline-block text-gray-500 mb-1 text-xs font-normal">Caption</label>
          <input type="text" class="control-input" wire:model="model.caption" spellcheck="false">
        </div>
        <div class="mb-2">
          <label class="inline-block text-gray-500 mb-1 text-xs font-normal">Description</label>
          <textarea class="control-input focus:ring-0" wire:model="model.description" rows="4" spellcheck="false"></textarea>
        </div>
      </div>
      <div class="flex items-center justify-between flex-nowrap px-0 py-1">
        <button type="reset" class="relative flex items-center justify-center px-3 py-1.5 bg-[#555] text-white rounded-md text-xs font-normal cursor-pointer transition-all duration-100 ease-in hover:text-white focus:outline-none">Reset</button>
        <button type="submit" class="relative flex items-center justify-center px-4 py-1.5 gap-x-2 bg-[#555] text-white rounded-full text-xs font-normal cursor-pointer transition-all duration-100 ease-in" wire:click="createAttachments()" wire:loading.attr="disabled">
          <span wire:loading.remove wire:target="updateAttachment">Submit</span>
          <span wire:loading wire:target="updateAttachment">Saving...</span>
        </button>
      </div>
    </form>
  </div>

  <div class="absolute bottom-0 bg-[#E6E6E6] border-t border-[#ccc] w-full h-[50px] min-h-[50px]">
    <div class="flex items-center justify-start px-4 h-full w-full gap-x-2"></div>
  </div>
</div>