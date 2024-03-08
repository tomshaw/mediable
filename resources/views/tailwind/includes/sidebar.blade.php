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
          <input type="text" class="control-input" wire:model="attachment.sortorder" spellcheck="false">
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
      <div class="flex flex-col items-start justify-start flex-nowrap gap-y-2 p-0 m-0">
        <button type="reset" class="relative flex items-center justify-center w-full px-4 py-1.5 gap-x-2 bg-[#555] text-white rounded-full text-xs font-normal cursor-pointer transition-all duration-100 ease-in">Reset</button>
        <button type="submit" class="relative flex items-center justify-center w-full px-4 py-1.5 gap-x-2 bg-[#555] text-white rounded-full text-xs font-normal cursor-pointer transition-all duration-100 ease-in" wire:click="createAttachments()" wire:loading.attr="disabled">
          <span wire:loading.remove wire:target="updateAttachment">Submit</span>
          <span wire:loading wire:target="updateAttachment">Saving...</span>
        </button>
      </div>
    </form>
  </div>

  <div class="bg-gray-200 w-full h-[50px] min-h-[50px] max-h-[50px]">
    <div class="flex items-center justify-start px-4 h-full w-full gap-x-2"></div>
  </div>
</div>