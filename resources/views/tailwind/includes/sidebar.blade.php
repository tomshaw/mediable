<div class="flex flex-col justify-between p-0 m-0 w-full h-full">

    <div class="bg-gray-200 h-10 min-h-10 max-h-10 xl:h-11 xl:min-h-11 xl:max-h-11 2xl:h-12 2xl:min-h-12 2xl:max-h-12 w-full">
        <div class="flex items-center justify-between px-4 h-full w-full">
            <div></div>
            <div></div>
        </div>
    </div>

    <div class="flex-grow border-b border-t border-[#ccc] scrollY h-auto p-2">
        <div class="flex items-start justify-center p-0 m-0 w-full h-full">
            <form class="w-full" wire:submit.prevent="updateAttachment" role="form">
                <div class="p-2 m-0">
                    <div class="mb-1">
                        <label class="inline-block text-gray-500 mb-1 text-xs font-normal tracking-wide">Title</label>
                        <input type="text" class="control-input" wire:model="attachment.title" spellcheck="false">
                    </div>
                    <div class="mb-1">
                        <label class="inline-block text-gray-500 mb-1 text-xs font-normal tracking-wide">Caption</label>
                        <input type="text" class="control-input" wire:model="attachment.caption" spellcheck="false">
                    </div>
                    <div class="mb-1">
                        <label class="inline-block text-gray-500 mb-1 text-xs font-normal tracking-wide">Order</label>
                        <input type="text" class="control-input" wire:model="attachment.sort_order" spellcheck="false">
                    </div>
                    <div class="mb-1">
                        <label class="inline-block text-gray-500 mb-1 text-xs font-normal tracking-wide">Styles</label>
                        <input type="text" class="control-input" wire:model="attachment.styles" spellcheck="false">
                    </div>
                    <div class="mb-1">
                        <label class="inline-block text-gray-500 mb-1 text-xs font-normal tracking-wide">Description</label>
                        <textarea class="control-input focus:ring-0" wire:model="attachment.description" rows="4" spellcheck="false"></textarea>
                    </div>
                </div>
                <div class="flex flex-col items-start justify-start flex-nowrap gap-y-2 p-0 mt-1">
                    <button type="reset" class="group relative inline-flex items-center justify-center overflow-hidden rounded-full bg-[#555] w-full py-1.5 px-4 font-normal text-xs text-neutral-50">
                        <span class="absolute h-0 w-0 rounded-full bg-rose-400 transition-all duration-300 group-hover:h-full group-hover:w-full"></span>
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
    </div>

    <div class="bg-gray-200 h-10 min-h-10 max-h-10 xl:h-11 xl:min-h-11 xl:max-h-11 2xl:h-12 2xl:min-h-12 2xl:max-h-12 w-full">
        <div class="flex items-center justify-start px-4 h-full w-full gap-x-2"></div>
    </div>
</div>