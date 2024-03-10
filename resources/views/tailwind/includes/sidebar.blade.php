<div class="flex flex-col justify-between p-0 m-0 w-full h-full">

  <div class="bg-gray-200 w-full h-[50px] min-h-[50px] max-h-[50px]">
    <div class="flex items-center justify-between px-4 h-full w-full">
      <div></div>
      <div></div>
    </div>
  </div>

  <div class="flex-grow border-b border-t border-[#ccc] h-full p-4">
    <form wire:submit.prevent="updateAttachment" role="form">
      <div class="p-0 m-0">
        <div class="mb-2">
          <label class="inline-block text-gray-500 mb-1 text-xs font-normal">Title</label>
          <input type="text" class="control-input" wire:model="attachment.title" spellcheck="false">
        </div>
        <div class="mb-2">
          <label class="inline-block text-gray-500 mb-1 text-xs font-normal">Caption</label>
          <input type="text" class="control-input" wire:model="attachment.caption" spellcheck="false">
        </div>
        <div class="mb-2">
          <label class="inline-block text-gray-500 mb-1 text-xs font-bold tracking-wide">Order</label>
          <input type="text" class="control-input" wire:model="attachment.sort_order" spellcheck="false">
        </div>
        <div class="mb-2">
          <label class="inline-block text-gray-500 mb-1 text-xs font-bold tracking-wide">Styles</label>
          <input type="text" class="control-input" wire:model="attachment.styles" spellcheck="false">
        </div>
        <div class="mb-2">
          <label class="inline-block text-gray-500 mb-1 text-xs font-normal">Description</label>
          <textarea class="control-input focus:ring-0" wire:model="attachment.description" rows="4" spellcheck="false"></textarea>
        </div>
      </div>
      <div class="flex flex-col items-start justify-start flex-nowrap gap-y-2 p-0 mt-3">
        <button type="reset" class="group relative inline-flex items-center justify-center overflow-hidden rounded-full bg-[#555] w-full py-1.5 px-4 font-normal text-xs text-neutral-50">
          <span class="absolute h-0 w-0 rounded-full bg-[#777] transition-all duration-300 group-hover:h-full group-hover:w-full"></span>
          <span class="relative">Reset</span>
        </button>
        <button type="button" class="group relative inline-flex items-center justify-center overflow-hidden rounded-full bg-[#555] w-full py-1.5 px-4 font-normal text-xs text-neutral-50" wire:click="updateAttachment" wire:loading.attr="disabled">
          <span class="absolute h-0 w-0 rounded-full bg-blue-400 transition-all duration-300 group-hover:h-full group-hover:w-full"></span>
          <span class="relative" wire:loading.remove wire:target="updateAttachment">Submit</span>
          <span class="relative" wire:loading wire:target="updateAttachment">Saving...</span>
        </button>
      </div>
    </form>
  </div>

  <div class="bg-gray-200 w-full h-[50px] min-h-[50px] max-h-[50px]">
    <div class="flex items-center justify-start px-4 h-full w-full gap-x-2"></div>
  </div>
</div>