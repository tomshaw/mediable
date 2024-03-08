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
                <figure class="mb-0">
                    <img src="{{ $this->attachment->file_url }}?id={{ $uniqueId }}" class="object-cover" />
                </figure>
                @endif

                <div class="mb-1 w-full">
                    <label for="flipMode" class="inline-block text-gray-500 mb-1 text-xs font-normal">Image Flip:</label>
                    <select id="flipMode" class="block text-gray-600 border border-gray-300 w-full py-1 px-2 appearance-none rounded-md text-xs font-medium leading-5" wire:model.live="flipMode" wire:change="flipImage">
                        <option value="">Flip Modes</option>
                        @foreach($this->getFlipModes() as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-1 w-full">
                    <label for="scaleMode" class="inline-block text-gray-500 mb-1 text-xs font-normal">Image Scale:</label>
                    <select id="scaleMode" class="block text-gray-600 border border-gray-300 w-full py-1 px-2 appearance-none rounded-md text-xs font-medium leading-5" wire:model.live="scaleMode" wire:change="scaleImage">
                        <option value="">Scale Modes</option>
                        @foreach($this->getScaleModes() as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-1 w-full">
                    <label for="filterMode" class="inline-block text-gray-500 mb-1 text-xs font-normal">Image Filters:</label>
                    <select id="filterMode" class="block text-gray-600 border border-gray-300 w-full py-1 px-2 appearance-none rounded-md text-xs font-medium leading-5" wire:model.live="filterMode">
                        <option value="">Filter Modes</option>
                        @foreach($this->getFilterModes() as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>

                @if(in_array($scaleMode, array_keys($this->getScaleModes())))
                <div class="mb-1 w-full">
                    <label for="newWidth" class="inline-block text-gray-500 mb-1 text-xs font-normal">Width:</label>
                    <input type="number" class="control-input w-full" id="newWidth" wire:model.live="newWidth" wire:change="scaleImage">
                </div>
                <div class="mb-1 w-full">
                    <label for="newHeight" class="inline-block text-gray-500 mb-1 text-xs font-normal">Height:</label>
                    <input type="number" class="control-input w-full" id="newHeight" wire:model.live="newHeight" wire:change="scaleImage">
                </div>
                @endif

                @if($filterMode == IMG_FILTER_CONTRAST)
                <div class="mb-1 w-full">
                    <label for="contrast" class="inline-block text-gray-500 mb-1 text-xs font-normal">Contrast:</label>
                    <input type="number" class="control-input w-full" id="contrast" wire:model.live="contrast" min="-100" max="100" step="1" />
                </div>
                @endif

                @if($filterMode == IMG_FILTER_BRIGHTNESS)
                <div class="mb-1 w-full">
                    <label for="brightness" class="inline-block text-gray-500 mb-1 text-xs font-normal">Brightness:</label>
                    <input type="number" class="control-input w-full" id="brightness" wire:model.live="brightness" min="-255" max="255" step="1" />
                </div>
                @endif

                @if($filterMode == IMG_FILTER_COLORIZE)
                <div class="mb-1 w-full">
                    <label for="colorize" class="inline-block text-gray-500 mb-1 text-xs font-normal">Colorize Color:</label>
                    <input type="color" class="control-input w-full" id="colorize" wire:model.live="colorize" />
                </div>
                @endif

                @if($filterMode == IMG_FILTER_SMOOTH)
                <div class="mb-1 w-full">
                    <label for="smoothLevel" class="inline-block text-gray-500 mb-1 text-xs font-normal">Smooth Level:</label>
                    <input type="number" class="control-input w-full" id="smoothLevel" wire:model.live="smoothLevel" min="-10" max="10" step="1" />
                </div>
                @endif

                @if($filterMode == IMG_FILTER_PIXELATE)
                <div class="mb-1 w-full">
                    <label for="pixelateBlockSize" class="inline-block text-gray-500 mb-1 text-xs font-normal">Pixelate Block Size:</label>
                    <input type="number" class="control-input w-full" id="pixelateBlockSize" wire:model.live="pixelateBlockSize" min="1" step="1" />
                </div>
                @endif

                <div class="w-full mt-2">
                    <button type="submit" wire:loading.attr="disabled" class="relative flex items-center justify-center w-full px-4 py-1.5 gap-x-2 bg-[#555] text-white rounded-full text-xs font-normal cursor-pointer transition-all duration-100 ease-in" wire:click="filterImage">
                        <span wire:loading.remove>Apply filter</span>
                        <span wire:loading>Processing...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-gray-200 w-full h-[50px] min-h-[50px] max-h-[50px]">
        <div class="flex items-center justify-start px-4 h-full w-full gap-x-2"></div>
    </div>
</div>