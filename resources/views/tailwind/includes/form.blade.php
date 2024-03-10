<div class="flex flex-col justify-between p-0 m-0 w-full h-full">

    <div class="bg-gray-200 w-full h-[50px] min-h-[50px] max-h-[50px]">
        <div class="flex items-center justify-between px-4 h-full w-full">
            <div></div>
            <div></div>
        </div>
    </div>

    <div class="flex-grow border-b border-t border-[#ccc] h-full p-2">
        <div class="flex items-start justify-center p-0 m-0 w-full h-full overflow-hidden">
            <div class="flex flex-col items-start justify-start w-full p-2">

                @if ($this->mimeTypeImage($this->attachment->file_type))
                <figure class="mb-2">
                    <img src="{{ $this->attachment->file_url }}?id={{ $uniqueId }}" class="object-cover" />
                </figure>
                @endif

                @if($selectedForm == '')
                @foreach($this->availableForms as $key => $value)
                <button type="button" wire:click="setForm('{{$key}}')" class="text-xs text-white overflow-hidden w-full select-none mb-1.5 py-1 px-2 bg-[#444] hover:bg-[#555]">
                    {{ $value }}
                </button>
                @endforeach
                @endif

                @if($selectedForm == 'image-flip')
                <div class="mb-1 w-full">
                    <label class="inline-block text-gray-500 mb-1 text-xs font-normal" for="flipMode">Image Flip:</label>
                    <select wire:model.live="flipMode" class="block text-gray-600 border border-gray-300 w-full py-1 px-2 appearance-none rounded-md text-xs font-medium leading-5" id="flipMode">
                        <option value="">Flip Modes</option>
                        @foreach($this->getFlipModes() as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col justify-start items-stretch w-full mt-2 gap-y-2">
                    <button type="submit" wire:click="flipImage" wire:loading.attr="disabled" class="relative flex items-center justify-center w-full px-4 py-1.5 gap-x-2 bg-[#555] text-white rounded-full text-xs font-normal cursor-pointer transition-all duration-100 ease-in">
                        <span wire:loading.remove wire:target="flipImage">Apply</span>
                        <span wire:loading wire:target="flipImage">Processing...</span>
                    </button>
                    @if(count($editHistory))
                    <button type="button" wire:click="undoEditorChanges" class="relative flex items-center justify-center px-4 py-1.5 gap-x-2 bg-[#555] text-white rounded-full text-xs font-normal cursor-pointer transition-all duration-100 ease-in">Undo</button>
                    <button type="button" wire:click="saveEditorChanges" class="relative flex items-center justify-center px-4 py-1.5 gap-x-2 bg-[#555] text-white rounded-full text-xs font-normal cursor-pointer transition-all duration-100 ease-in">Save</button>
                    @endif
                    <button type="button" wire:click="resetForm" class="group relative inline-flex items-center justify-center overflow-hidden rounded-full bg-[#555] w-full py-1.5 px-4 font-normal text-xs text-neutral-50">
                        <span class="absolute h-0 w-0 rounded-full bg-rose-400 transition-all duration-300 group-hover:h-full group-hover:w-full"></span>
                        <span class="relative">Back</span>
                    </button>
                </div>
                @endif

                @if($selectedForm == 'image-scale')
                <div class="mb-1 w-full">
                    <label for="scaleMode" class="inline-block text-gray-500 mb-1 text-xs font-normal">Scale mode:</label>
                    <select id="scaleMode" class="block text-gray-600 border border-gray-300 w-full py-1 px-2 appearance-none rounded-md text-xs font-medium leading-5" wire:model="scaleMode" wire:change="scaleImage">
                        <option value="">Scale Modes</option>
                        @foreach($this->getScaleModes() as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-1 w-full">
                    <label for="scaleWidth" class="inline-block text-gray-500 mb-1 text-xs font-normal">Width:</label>
                    <input type="number" class="control-input w-full" id="scaleWidth" wire:model.live.debounce.500ms="scaleWidth">
                </div>
                <div class="mb-1 w-full">
                    <label for="scaleHeight" class="inline-block text-gray-500 mb-1 text-xs font-normal">Height:</label>
                    <input type="number" class="control-input w-full" id="scaleHeight" wire:model.live.debounce.500ms="scaleHeight">
                </div>
                <div class="flex flex-col justify-start items-stretch w-full mt-2 gap-y-2">
                    <button type="submit" wire:click="scaleImage" wire:loading.attr="disabled" class="relative flex items-center justify-center w-full px-4 py-1.5 gap-x-2 bg-[#555] text-white rounded-full text-xs font-normal cursor-pointer transition-all duration-100 ease-in">
                        <span wire:loading.remove wire:target="scaleImage">Apply</span>
                        <span wire:loading wire:target="scaleImage">Processing...</span>
                    </button>
                    @if(count($editHistory))
                    <button type="button" wire:click="undoEditorChanges" class="relative flex items-center justify-center px-4 py-1.5 gap-x-2 bg-[#555] text-white rounded-full text-xs font-normal cursor-pointer transition-all duration-100 ease-in">Undo</button>
                    <button type="button" wire:click="saveEditorChanges" class="relative flex items-center justify-center px-4 py-1.5 gap-x-2 bg-[#555] text-white rounded-full text-xs font-normal cursor-pointer transition-all duration-100 ease-in">Save</button>
                    @endif
                    <button type="button" wire:click="resetForm" class="group relative inline-flex items-center justify-center overflow-hidden rounded-full bg-[#555] w-full py-1.5 px-4 font-normal text-xs text-neutral-50">
                        <span class="absolute h-0 w-0 rounded-full bg-rose-400 transition-all duration-300 group-hover:h-full group-hover:w-full"></span>
                        <span class="relative">Back</span>
                    </button>
                </div>
                @endif

                @if($selectedForm == 'image-filter')
                <div class="mb-1 w-full">
                    <label for="filterMode" class="inline-block text-gray-500 mb-1 text-xs font-normal">Image Filters:</label>
                    <select id="filterMode" class="block text-gray-600 border border-gray-300 w-full py-1 px-2 appearance-none rounded-md text-xs font-medium leading-5" wire:model.live="filterMode">
                        <option value="">Filter Modes</option>
                        @foreach($this->getFilterModes() as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                @if($filterMode == IMG_FILTER_CONTRAST)
                <div class="mb-1 w-full">
                    <label for="contrast" class="inline-block text-gray-500 mb-1 text-xs font-normal">Contrast:</label>
                    <input type="number" class="control-input w-full" id="contrast" wire:model="contrast" min="-100" max="100" step="1" />
                </div>
                @endif
                @if($filterMode == IMG_FILTER_BRIGHTNESS)
                <div class="mb-1 w-full">
                    <label for="brightness" class="inline-block text-gray-500 mb-1 text-xs font-normal">Brightness:</label>
                    <input type="number" class="control-input w-full" id="brightness" wire:model="brightness" min="-255" max="255" step="1" />
                </div>
                @endif
                @if($filterMode == IMG_FILTER_COLORIZE)
                <div class="mb-1 w-full">
                    <label for="colorize" class="inline-block text-gray-500 mb-1 text-xs font-normal">Colorize Color:</label>
                    <input type="color" class="control-input w-full" id="colorize" wire:model="colorize" />
                </div>
                @endif
                @if($filterMode == IMG_FILTER_SMOOTH)
                <div class="mb-1 w-full">
                    <label for="smoothLevel" class="inline-block text-gray-500 mb-1 text-xs font-normal">Smooth Level:</label>
                    <input type="number" class="control-input w-full" id="smoothLevel" wire:model="smoothLevel" min="-10" max="10" step="1" />
                </div>
                @endif
                @if($filterMode == IMG_FILTER_PIXELATE)
                <div class="mb-1 w-full">
                    <label for="pixelateBlockSize" class="inline-block text-gray-500 mb-1 text-xs font-normal">Pixelate Block Size:</label>
                    <input type="number" class="control-input w-full" id="pixelateBlockSize" wire:model="pixelateBlockSize" min="1" step="1" />
                </div>
                @endif
                <div class="flex flex-col justify-start items-stretch w-full mt-2 gap-y-2">
                    <button type="submit" wire:click="filterImage" wire:loading.attr="disabled" class="relative flex items-center justify-center w-full px-4 py-1.5 gap-x-2 bg-[#555] text-white rounded-full text-xs font-normal cursor-pointer transition-all duration-100 ease-in">
                        <span wire:loading.remove wire:target="filterImage">Apply</span>
                        <span wire:loading wire:target="filterImage">Processing...</span>
                    </button>
                    @if(count($editHistory))
                    <button type="button" wire:click="undoEditorChanges" class="relative flex items-center justify-center px-4 py-1.5 gap-x-2 bg-[#555] text-white rounded-full text-xs font-normal cursor-pointer transition-all duration-100 ease-in">Undo</button>
                    <button type="button" wire:click="saveEditorChanges" class="relative flex items-center justify-center px-4 py-1.5 gap-x-2 bg-[#555] text-white rounded-full text-xs font-normal cursor-pointer transition-all duration-100 ease-in">Save</button>
                    @endif
                    <button type="button" wire:click="resetForm" class="group relative inline-flex items-center justify-center overflow-hidden rounded-full bg-[#555] w-full py-1.5 px-4 font-normal text-xs text-neutral-50">
                        <span class="absolute h-0 w-0 rounded-full bg-rose-400 transition-all duration-300 group-hover:h-full group-hover:w-full"></span>
                        <span class="relative">Back</span>
                    </button>
                </div>
                @endif

                @if($selectedForm == 'image-rotate')
                <div class="mb-1 w-full">
                    <label for="rotateAngle" class="inline-block text-gray-500 mb-1 text-xs font-normal">Enter rotation amount (in degrees):</label>
                    <input type="range" class="control-input w-full" id="rotateAngle" wire:model="rotateAngle" min="0" max="360">
                </div>
                <div class="mb-1 w-full">
                    <label for="rotateBgColor" class="inline-block text-gray-500 mb-1 text-xs font-normal">Background Color:</label>
                    <input type="color" class="control-input w-full" id="rotateBgColor" wire:model="rotateBgColor" />
                </div>
                <div class="mb-1 w-full">
                    <label for="rotateIgnoreTransparent" class="inline-block text-gray-500 mb-1 text-xs font-normal">Ignore Transparent:</label>
                    <input type="checkbox" class="control-input w-full" id="rotateIgnoreTransparent" wire:model="rotateIgnoreTransparent" />
                </div>
                <div class="flex flex-col justify-start items-stretch w-full mt-2 gap-y-2">
                    <button type="submit" wire:click="rotateImage" wire:loading.attr="disabled" class="relative flex items-center justify-center w-full px-4 py-1.5 gap-x-2 bg-[#555] text-white rounded-full text-xs font-normal cursor-pointer transition-all duration-100 ease-in">
                        <span wire:loading.remove wire:target="rotateImage">Apply</span>
                        <span wire:loading wire:target="rotateImage">Processing...</span>
                    </button>
                    @if(count($editHistory))
                    <button type="button" wire:click="undoEditorChanges" class="relative flex items-center justify-center px-4 py-1.5 gap-x-2 bg-[#555] text-white rounded-full text-xs font-normal cursor-pointer transition-all duration-100 ease-in">Undo</button>
                    <button type="button" wire:click="saveEditorChanges" class="relative flex items-center justify-center px-4 py-1.5 gap-x-2 bg-[#555] text-white rounded-full text-xs font-normal cursor-pointer transition-all duration-100 ease-in">Save</button>
                    @endif
                    <button type="button" wire:click="resetForm" class="group relative inline-flex items-center justify-center overflow-hidden rounded-full bg-[#555] w-full py-1.5 px-4 font-normal text-xs text-neutral-50">
                        <span class="absolute h-0 w-0 rounded-full bg-rose-400 transition-all duration-300 group-hover:h-full group-hover:w-full"></span>
                        <span class="relative">Back</span>
                    </button>
                </div>
                @endif

                @if($selectedForm == 'image-crop')
                <div class="mb-1 w-full">
                    <label for="cropX" class="inline-block text-gray-500 mb-1 text-xs font-normal">X Coordinate:</label>
                    <input type="number" class="control-input w-full" id="cropX" wire:model="cropX">
                </div>
                <div class="mb-1 w-full">
                    <label for="cropY" class="inline-block text-gray-500 mb-1 text-xs font-normal">Y Coordinate:</label>
                    <input type="number" class="control-input w-full" id="cropY" wire:model="cropY">
                </div>
                <div class="mb-1 w-full">
                    <label for="cropWidth" class="inline-block text-gray-500 mb-1 text-xs font-normal">Width:</label>
                    <input type="number" class="control-input w-full" id="cropWidth" wire:model="cropWidth">
                </div>
                <div class="mb-1 w-full">
                    <label for="cropHeight" class="inline-block text-gray-500 mb-1 text-xs font-normal">Height:</label>
                    <input type="number" class="control-input w-full" id="cropHeight" wire:model="cropHeight">
                </div>
                <div class="flex flex-col justify-start items-stretch w-full mt-2 gap-y-2">
                    <button type="submit" wire:click="cropImage" wire:loading.attr="disabled" class="relative flex items-center justify-center w-full px-4 py-1.5 gap-x-2 bg-[#555] text-white rounded-full text-xs font-normal cursor-pointer transition-all duration-100 ease-in">
                        <span wire:loading.remove wire:target="cropImage">Apply</span>
                        <span wire:loading wire:target="cropImage">Processing...</span>
                    </button>
                    @if(count($editHistory))
                    <button type="button" wire:click="undoEditorChanges" class="relative flex items-center justify-center px-4 py-1.5 gap-x-2 bg-[#555] text-white rounded-full text-xs font-normal cursor-pointer transition-all duration-100 ease-in">Undo</button>
                    <button type="button" wire:click="saveEditorChanges" class="relative flex items-center justify-center px-4 py-1.5 gap-x-2 bg-[#555] text-white rounded-full text-xs font-normal cursor-pointer transition-all duration-100 ease-in">Save</button>
                    @endif
                    <button type="button" wire:click="resetForm" class="group relative inline-flex items-center justify-center overflow-hidden rounded-full bg-[#555] w-full py-1.5 px-4 font-normal text-xs text-neutral-50">
                        <span class="absolute h-0 w-0 rounded-full bg-rose-400 transition-all duration-300 group-hover:h-full group-hover:w-full"></span>
                        <span class="relative">Back</span>
                    </button>
                </div>
                @endif

            </div>
        </div>
    </div>

    <div class="bg-gray-200 w-full h-[50px] min-h-[50px] max-h-[50px]">
        <div class="flex items-center justify-start px-4 h-full w-full gap-x-2"></div>
    </div>
</div>