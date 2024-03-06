<div class="flex items-center justify-between h-full w-full px-4">

    @if($uploadMode)
    <div class="flex items-center justify-start gap-2">
        <button type="button" wire:click="enableThumbMode" class="relative flex items-center justify-center px-4 py-1.5 gap-x-2 bg-[#555] text-white rounded-full text-xs font-normal cursor-pointer transition-all duration-100 ease-in">Close</button>
    </div>
    <div class="flex items-center justify-end gap-2">
        <button type="button" class="relative flex items-center justify-center px-4 py-1.5 gap-x-2 bg-[#555] text-white rounded-full text-xs font-normal cursor-pointer transition-all duration-100 ease-in" wire:click="clearFiles()">Reset</button>
        <button type="button" class="relative flex items-center justify-center px-4 py-1.5 gap-x-2 bg-[#555] text-white rounded-full text-xs font-normal cursor-pointer transition-all duration-100 ease-in" wire:click="createAttachments()" wire:loading.attr="disabled">
            <span wire:loading.remove>Add Attachments</span>
            <span wire:loading>Processing...</span>
        </button>
    </div>
    @endif

    @if($thumbMode)
    <div class="flex items-center justify-start gap-2">
        <div wire:loading class="hidden">
            <div class="border-gray-300 h-6 w-6 animate-spin rounded-full border-2 border-t-blue-600"></div>
        </div>
    </div>
    <div class="flex items-center justify-end gap-2">
        <button type="button" wire:click="enableUploadMode" class="relative flex items-center justify-center px-4 py-1.5 gap-x-2 bg-[#555] text-white rounded-full text-xs font-normal cursor-pointer transition-all duration-100 ease-in">Uploads</button>
    </div>
    @endif

</div>