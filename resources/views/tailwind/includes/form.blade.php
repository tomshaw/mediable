<div class="flex w-full h-full p-0 m-0 overflow-hidden">
    <div class="w-10/12 flex items-center justify-center h-auto bg-[#f3f4f6] border-r border-[#ccc]">
        <div class="flex items-center justify-center max-w-5xl">
            @if ($this->mimeTypeImage($this->model->fileType))
            <div class="flex items-center justify-center ">
                <img src="{{ asset($this->model->fileUrl) }}?id={{ $uniqueId }}" class="object-contain shadow-md w-full h-full" data-id={{$this->model->id}}>
            </div>
            @elseif ($this->mimeTypeVideo($this->model->fileType))
            <div class="flex items-center justify-center h-full w-full">
                <video src="{{ asset($this->model->fileUrl) }}" controls class="w-full h-auto object-contain"></video>
            </div>
            @elseif ($this->mimeTypeAudio($this->model->fileType))
            <div class="flex items-center justify-center h-full w-full">
                <audio controls>
                    <source src="{{ asset($this->model->fileUrl) }}" type="{{ $this->model->fileType }}">
                </audio>
            </div>
            @endif
        </div>
    </div>
    <div class="w-2/12 bg-[#e5e7eb] p-7">
        <form wire:submit="updateAttachment" role="form">
            <div class="p-0 m-0">
                <div class="mb-2">
                    <label class="inline-block text-gray-500 mb-1 text-xs font-bold tracking-wide">Title</label>
                    <input type="text" class="control-input" wire:model="model.title" spellcheck="false">
                </div>
                <div class="mb-2">
                    <label class="inline-block text-gray-500 mb-1 text-xs font-bold tracking-wide">Caption</label>
                    <input type="text" class="control-input" wire:model="model.caption" spellcheck="false">
                </div>
                <div class="mb-2">
                    <label class="inline-block text-gray-500 mb-1 text-xs font-bold tracking-wide">Order</label>
                    <input type="text" class="control-input" wire:model="model.sortorder" spellcheck="false">
                </div>
                <div class="mb-2">
                    <label class="inline-block text-gray-500 mb-1 text-xs font-bold tracking-wide">Styles</label>
                    <input type="text" class="control-input" wire:model="model.styles" spellcheck="false">
                </div>
                <div class="mb-2">
                    <label class="inline-block text-gray-500 mb-1 text-xs font-bold tracking-wide">Description</label>
                    <textarea class="control-input focus:ring-0" wire:model="model.description" rows="4" spellcheck="false"></textarea>
                </div>
            </div>
            <div class="flex items-center justify-between flex-nowrap px-0 py-1">
                <button type="reset" class="relative flex items-center justify-center px-4 py-1.5 gap-x-2 bg-[#555] text-white rounded-full text-xs font-normal cursor-pointer transition-all duration-100 ease-in">Reset</button>
                <button type="submit" wire:loading.attr="disabled" class="relative flex items-center justify-center px-4 py-1.5 gap-x-2 bg-[#555] text-white rounded-full text-xs font-normal cursor-pointer transition-all duration-100 ease-in">
                    <span wire:loading.remove>Submit</span>
                    <span wire:loading>Processing...</span>
                </button>
            </div>
        </form>
    </div>
</div>