@props([
    'action',
    'target',
    'showHistory' => false,
])

<div class="flex flex-col justify-start items-stretch w-full mt-2 gap-y-1.5">
    <button wire:click="{{ $action }}" wire:loading.attr="disabled" class="inline-flex items-center justify-center h-8 rounded-lg bg-indigo-600 px-3 text-xs font-medium text-white hover:bg-indigo-500 cursor-pointer transition-colors shadow-sm disabled:opacity-60 disabled:cursor-wait">
        <span wire:loading.remove wire:target="{{ $target }}">Apply</span>
        <span wire:loading wire:target="{{ $target }}">Processing&hellip;</span>
    </button>
    @if($showHistory)
        <button type="button" wire:click="undoEditorChanges" class="inline-flex items-center justify-center h-8 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 text-xs font-medium text-zinc-700 dark:text-zinc-200 hover:border-zinc-400 dark:hover:border-zinc-500 cursor-pointer transition-colors">
            Undo
        </button>
        <button type="button" wire:click="saveEditorChanges" class="inline-flex items-center justify-center h-8 rounded-lg bg-zinc-900 dark:bg-zinc-100 px-3 text-xs font-medium text-white dark:text-zinc-900 hover:bg-zinc-700 dark:hover:bg-white cursor-pointer transition-colors">
            Save changes
        </button>
    @endif
    <button type="button" wire:click="resetForm" class="inline-flex items-center justify-center h-8 rounded-lg px-3 text-xs font-medium text-zinc-600 hover:bg-zinc-200/70 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-100 cursor-pointer transition-colors">
        Back
    </button>
</div>
