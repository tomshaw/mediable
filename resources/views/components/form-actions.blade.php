@props([
    'action',
    'target',
    'showHistory' => false,
])

<div class="flex flex-col justify-start items-stretch w-full mt-2 gap-y-2">
    <button wire:click="{{ $action }}" wire:loading.attr="disabled" class="group relative inline-flex items-center justify-center overflow-hidden rounded-full font-medium text-xs tracking-wider text-neutral-50 cursor-pointer bg-[#555] py-1.5 px-3">
        <span class="absolute h-0 w-0 rounded-full bg-blue-400 transition-all duration-300 group-hover:h-56 group-hover:w-full"></span>
        <span class="relative" wire:loading.remove wire:target="{{ $target }}">Apply</span>
        <span class="relative" wire:loading wire:target="{{ $target }}">Processing...</span>
    </button>
    @if($showHistory)
        <button type="button" wire:click="undoEditorChanges" class="group relative inline-flex items-center justify-center overflow-hidden rounded-full bg-[#555] w-full py-1.5 px-4 font-medium text-xs tracking-wider text-neutral-50 cursor-pointer">
            <span class="absolute h-0 w-0 rounded-full bg-[#444] transition-all duration-300 group-hover:h-full group-hover:w-full"></span>
            <span class="relative">Undo</span>
        </button>
        <button type="button" wire:click="saveEditorChanges" class="group relative inline-flex items-center justify-center overflow-hidden rounded-full bg-[#555] w-full py-1.5 px-4 font-medium text-xs tracking-wider text-neutral-50 cursor-pointer">
            <span class="absolute h-0 w-0 rounded-full bg-[#444] transition-all duration-300 group-hover:h-full group-hover:w-full"></span>
            <span class="relative">Save</span>
        </button>
    @endif
    <button type="button" wire:click="resetForm" class="group relative inline-flex items-center justify-center overflow-hidden rounded-full bg-[#555] w-full py-1.5 px-4 font-medium text-xs tracking-wider text-neutral-50 cursor-pointer">
        <span class="absolute h-0 w-0 rounded-full bg-[#444] transition-all duration-300 group-hover:h-full group-hover:w-full"></span>
        <span class="relative">Back</span>
    </button>
</div>
