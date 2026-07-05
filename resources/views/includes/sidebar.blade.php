<div class="flex flex-col justify-between p-0 m-0 w-full h-full">

    <div class="bg-gray-200 h-10 min-h-10 max-h-10 xl:h-11 xl:min-h-11 xl:max-h-11 2xl:h-12 2xl:min-h-12 2xl:max-h-12 w-full">
        <div class="flex items-center justify-between px-4 h-full w-full">
            <div></div>
            <div></div>
        </div>
    </div>

    <div class="grow border-b border-t border-gray-300 scrollY h-auto p-2">
        <div class="flex items-start justify-center p-0 m-0 w-full h-full">
            <form class="w-full" wire:submit.prevent="updateAttachment" role="form">
                <div class="p-2 m-0">
                    <x-mediable::form-input label="Title" id="title" wire:model="title" spellcheck="false" />
                    <x-mediable::form-input label="Caption" id="caption" wire:model="caption" spellcheck="false" />
                    <x-mediable::form-input label="Order" id="sort_order" wire:model="sort_order" spellcheck="false" />
                    <x-mediable::form-input label="Styles" id="styles" wire:model="styles" spellcheck="false" />
                    <x-mediable::form-textarea label="Description" id="description" :rows="4" wire:model="description" spellcheck="false" />
                </div>
                <div class="flex flex-col items-start justify-start flex-nowrap gap-y-2 p-0 mt-1">
                    <button type="reset" class="group relative inline-flex items-center justify-center overflow-hidden rounded-full bg-neutral-900 w-full py-1.5 px-4 font-medium text-xs tracking-wider text-neutral-50 cursor-pointer">
                        <span class="absolute h-0 w-0 rounded-full bg-rose-400 transition-all duration-300 group-hover:h-full group-hover:w-full"></span>
                        <span class="relative">Reset</span>
                    </button>
                    <button type="button" class="group relative inline-flex items-center justify-center overflow-hidden rounded-full bg-neutral-900 w-full py-1.5 px-4 font-medium text-xs tracking-wider text-neutral-50 cursor-pointer" wire:click="updateAttachment" wire:loading.attr="disabled">
                        <span class="absolute h-0 w-0 rounded-full bg-blue-400 transition-all duration-300 group-hover:h-full group-hover:w-full"></span>
                        <span class="spinner relative" wire:loading wire:target="updateAttachment"></span>
                        <span class="relative" wire:loading.remove wire:target="updateAttachment">Submit</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="bg-gray-200 h-10 min-h-10 max-h-10 xl:h-11 xl:min-h-11 xl:max-h-11 2xl:h-12 2xl:min-h-12 2xl:max-h-12 w-full">
        <div class="flex items-center justify-start px-4 h-full w-full gap-x-2"></div>
    </div>
</div>
