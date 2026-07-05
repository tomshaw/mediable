<div class="flex flex-col justify-between p-0 m-0 w-full h-full">

    <div class="h-12 min-h-12 max-h-12 w-full">
        <div class="flex items-center justify-between px-4 h-full w-full">
            <span class="text-[11px] font-medium uppercase tracking-widest text-zinc-400 dark:text-zinc-500 select-none">Details</span>
            @if($activeId)
            <span class="font-mono text-[10px] text-zinc-400 dark:text-zinc-500 select-none">#{{ $activeId }}</span>
            @endif
        </div>
    </div>

    <div class="grow border-b border-t border-zinc-200 dark:border-zinc-800 scrollY h-auto p-2">
        <div class="flex items-start justify-center p-0 m-0 w-full h-full">
            <form class="w-full" wire:submit.prevent="updateAttachment" role="form">
                <div class="p-2 m-0 flex flex-col gap-y-2.5">
                    <x-mediable::form-input label="Title" id="title" wire:model="title" spellcheck="false" />
                    <x-mediable::form-input label="Caption" id="caption" wire:model="caption" spellcheck="false" />
                    <x-mediable::form-input label="Sort order" id="sort_order" wire:model="sort_order" spellcheck="false" />
                    <x-mediable::form-input label="CSS styles" id="styles" wire:model="styles" spellcheck="false" />
                    <x-mediable::form-textarea label="Description" id="description" :rows="4" wire:model="description" spellcheck="false" />
                </div>
                <div class="flex flex-col items-stretch justify-start flex-nowrap gap-y-2 px-2 mt-2">
                    <button type="button" class="inline-flex items-center justify-center h-8 rounded-lg bg-zinc-900 dark:bg-zinc-100 px-4 text-xs font-medium text-white dark:text-zinc-900 hover:bg-zinc-700 dark:hover:bg-white cursor-pointer transition-colors" wire:click="updateAttachment" wire:loading.attr="disabled">
                        <span class="spinner" wire:loading wire:target="updateAttachment"></span>
                        <span wire:loading.remove wire:target="updateAttachment">Save changes</span>
                    </button>
                    <button type="reset" class="inline-flex items-center justify-center h-8 rounded-lg px-4 text-xs font-medium text-zinc-600 hover:bg-zinc-200/70 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-100 cursor-pointer transition-colors">
                        Reset
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="h-12 min-h-12 max-h-12 w-full">
        <div class="flex items-center justify-start px-4 h-full w-full gap-x-2"></div>
    </div>
</div>
