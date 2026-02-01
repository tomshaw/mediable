<?php

use Livewire\Attributes\{On, Reactive};
use Livewire\Component;
use TomShaw\Mediable\Concerns\AttachmentState;
use TomShaw\Mediable\Eloquent\Eloquent;
use TomShaw\Mediable\GraphicDraw\GraphicDraw;
use TomShaw\Mediable\Traits\{WithFonts, WithGraphicDraw};

new class extends Component {
    use WithFonts;
    use WithGraphicDraw;

    #[Reactive]
    public ?AttachmentState $attachment;

    public ?int $primaryId = null;

    public string $uniqueId = '';

    public function mount(string $uniqueId = ''): void
    {
        $this->uniqueId = $uniqueId;
        $this->initializeScaleDimensions();
    }

    public function updatedAttachment(): void
    {
        $this->initializeScaleDimensions();
    }

    public function initializeScaleDimensions(): void
    {
        if (! $this->attachment || ! $this->mimeTypeImage($this->attachment->file_type ?? '')) {
            return;
        }

        $filePath = Eloquent::getFilePath($this->attachment->file_dir);
        if (! file_exists($filePath)) {
            return;
        }

        [$width, $height, $type] = GraphicDraw::getimagesize($filePath);

        if ($type) {
            $this->scaleWidth = $width;
            $this->scaleHeight = $height;
        }
    }

    #[On('form:editor-prepared')]
    public function handleEditorPrepared(int $primaryId): void
    {
        $this->primaryId = $primaryId;
    }

    #[On('toolbar:close-image-editor')]
    public function handleEditorClosed(): void
    {
        $this->primaryId = null;
        $this->editHistory = [];
        $this->selectedForm = '';
    }

    #[On('panel:regenerate-unique-id')]
    public function handleRegenerateUniqueId(string $uniqueId): void
    {
        $this->uniqueId = $uniqueId;
    }

    public function generateUniqueId(): void
    {
        $this->uniqueId = uniqid();
        $this->dispatch('panel:unique-id-updated', uniqueId: $this->uniqueId);
    }

    public function saveEditorChanges(): void
    {
        if (! $this->attachment->id) {
            return;
        }

        Eloquent::enable($this->attachment->id);

        $this->primaryId = null;
        $this->editHistory = [];
        $this->selectedForm = '';

        $this->dispatch('form:editor-saved');
    }

    public function undoEditorChanges(): void
    {
        if (! $this->primaryId) {
            return;
        }

        $row = Eloquent::load($this->primaryId);

        $source = $row->file_dir;
        $destination = Eloquent::randomizeName($source);
        $destination = Eloquent::copyImageFromTo($source, $destination);

        $item = Eloquent::saveImageToDatabase($this->attachment, $destination);

        $this->attachment = AttachmentState::fromAttachment($item);
        $this->editHistory = [];

        $this->generateUniqueId();
    }

    public function mimeTypeImage(string $mimeType): bool
    {
        return str_starts_with($mimeType, 'image/');
    }
}; ?>

<div class="flex flex-col justify-between p-0 m-0 w-full h-full">

    <div class="bg-gray-200 h-10 min-h-10 max-h-10 xl:h-11 xl:min-h-11 xl:max-h-11 2xl:h-12 2xl:min-h-12 2xl:max-h-12 w-full">
        <div class="flex items-center justify-between px-4 h-full w-full">
            <div></div>
            <div></div>
        </div>
    </div>

    <div class="grow border-b border-t border-[#ccc] scrollY h-auto p-2">
        <div class="flex items-start justify-center p-0 m-0 w-full h-full">
            <div class="flex flex-col items-start justify-start w-full p-3">

                @if ($attachment && $this->mimeTypeImage($attachment->file_type))
                <figure class="w-full mb-3">
                    <img src="{{ $attachment->file_url }}?id={{ $uniqueId }}" class="w-full object-cover" />
                </figure>
                @endif

                @if($selectedForm == '')
                @foreach($availableForms as $key => $value)
                <button wire:click="setForm('{{$key}}')" class="group relative inline-flex items-center justify-center bg-[#555] hover:bg-[#444] rounded-full select-none appearance-none overflow-hidden h-7 w-full mb-1.5 font-medium text-xs tracking-wider text-neutral-50">
                    <span>{{ $value }}</span>
                    <div class="w-0 translate-x-full pl-0 opacity-0 transition-all duration-200 group-hover:w-5 group-hover:translate-x-0 group-hover:pl-1 group-hover:opacity-100">
                        <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5">
                            <path d="M8.14645 3.14645C8.34171 2.95118 8.65829 2.95118 8.85355 3.14645L12.8536 7.14645C13.0488 7.34171 13.0488 7.65829 12.8536 7.85355L8.85355 11.8536C8.65829 12.0488 8.34171 12.0488 8.14645 11.8536C7.95118 11.6583 7.95118 11.3417 8.14645 11.1464L11.2929 8H2.5C2.22386 8 2 7.77614 2 7.5C2 7.22386 2.22386 7 2.5 7H11.2929L8.14645 3.85355C7.95118 3.65829 7.95118 3.34171 8.14645 3.14645Z" fill="currentColor" fill-rule="evenodd" clip-rule="evenodd"></path>
                        </svg>
                    </div>
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
                    <button wire:click="flipImage" wire:loading.attr="disabled" class="group relative inline-flex items-center justify-center overflow-hidden rounded-full font-medium text-xs tracking-wider text-neutral-50 cursor-pointer bg-[#555] py-1.5 px-3">
                        <span class="absolute h-0 w-0 rounded-full bg-blue-400 transition-all duration-300 group-hover:h-56 group-hover:w-full"></span>
                        <span class="relative" wire:loading.remove wire:target="flipImage">Apply</span>
                        <span class="relative" wire:loading wire:target="flipImage">Processing...</span>
                    </button>
                    @if(count($editHistory))
                    <button type="button" wire:click="undoEditorChanges" class="group relative inline-flex items-center justify-center overflow-hidden rounded-full bg-[#555] w-full py-1.5 px-4 font-medium text-xs tracking-wider text-neutral-50">
                        <span class="absolute h-0 w-0 rounded-full bg-[#444] transition-all duration-300 group-hover:h-full group-hover:w-full"></span>
                        <span class="relative">Undo</span>
                    </button>
                    <button type="button" wire:click="saveEditorChanges" class="group relative inline-flex items-center justify-center overflow-hidden rounded-full bg-[#555] w-full py-1.5 px-4 font-medium text-xs tracking-wider text-neutral-50">
                        <span class="absolute h-0 w-0 rounded-full bg-[#444] transition-all duration-300 group-hover:h-full group-hover:w-full"></span>
                        <span class="relative">Save</span>
                    </button>
                    @endif
                    <button type="button" wire:click="resetForm" class="group relative inline-flex items-center justify-center overflow-hidden rounded-full bg-[#555] w-full py-1.5 px-4 font-medium text-xs tracking-wider text-neutral-50">
                        <span class="absolute h-0 w-0 rounded-full bg-[#444] transition-all duration-300 group-hover:h-full group-hover:w-full"></span>
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
                    <button wire:click="scaleImage" wire:loading.attr="disabled" class="group relative inline-flex items-center justify-center overflow-hidden rounded-full font-medium text-xs tracking-wider text-neutral-50 cursor-pointer bg-[#555] py-1.5 px-3">
                        <span class="absolute h-0 w-0 rounded-full bg-blue-400 transition-all duration-300 group-hover:h-56 group-hover:w-full"></span>
                        <span class="relative" wire:loading.remove wire:target="scaleImage">Apply</span>
                        <span class="relative" wire:loading wire:target="scaleImage">Processing...</span>
                    </button>
                    @if(count($editHistory))
                    <button type="button" wire:click="undoEditorChanges" class="group relative inline-flex items-center justify-center overflow-hidden rounded-full bg-[#555] w-full py-1.5 px-4 font-medium text-xs tracking-wider text-neutral-50">
                        <span class="absolute h-0 w-0 rounded-full bg-[#444] transition-all duration-300 group-hover:h-full group-hover:w-full"></span>
                        <span class="relative">Undo</span>
                    </button>
                    <button type="button" wire:click="saveEditorChanges" class="group relative inline-flex items-center justify-center overflow-hidden rounded-full bg-[#555] w-full py-1.5 px-4 font-medium text-xs tracking-wider text-neutral-50">
                        <span class="absolute h-0 w-0 rounded-full bg-[#444] transition-all duration-300 group-hover:h-full group-hover:w-full"></span>
                        <span class="relative">Save</span>
                    </button>
                    @endif
                    <button type="button" wire:click="resetForm" class="group relative inline-flex items-center justify-center overflow-hidden rounded-full bg-[#555] w-full py-1.5 px-4 font-medium text-xs tracking-wider text-neutral-50">
                        <span class="absolute h-0 w-0 rounded-full bg-[#444] transition-all duration-300 group-hover:h-full group-hover:w-full"></span>
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
                    <button wire:click="filterImage" wire:loading.attr="disabled" class="group relative inline-flex items-center justify-center overflow-hidden rounded-full font-medium text-xs tracking-wider text-neutral-50 cursor-pointer bg-[#555] py-1.5 px-3">
                        <span class="absolute h-0 w-0 rounded-full bg-blue-400 transition-all duration-300 group-hover:h-56 group-hover:w-full"></span>
                        <span class="relative" wire:loading.remove wire:target="filterImage">Apply</span>
                        <span class="relative" wire:loading wire:target="filterImage">Processing...</span>
                    </button>
                    @if(count($editHistory))
                    <button type="button" wire:click="undoEditorChanges" class="group relative inline-flex items-center justify-center overflow-hidden rounded-full bg-[#555] w-full py-1.5 px-4 font-medium text-xs tracking-wider text-neutral-50">
                        <span class="absolute h-0 w-0 rounded-full bg-[#444] transition-all duration-300 group-hover:h-full group-hover:w-full"></span>
                        <span class="relative">Undo</span>
                    </button>
                    <button type="button" wire:click="saveEditorChanges" class="group relative inline-flex items-center justify-center overflow-hidden rounded-full bg-[#555] w-full py-1.5 px-4 font-medium text-xs tracking-wider text-neutral-50">
                        <span class="absolute h-0 w-0 rounded-full bg-[#444] transition-all duration-300 group-hover:h-full group-hover:w-full"></span>
                        <span class="relative">Save</span>
                    </button>
                    @endif
                    <button type="button" wire:click="resetForm" class="group relative inline-flex items-center justify-center overflow-hidden rounded-full bg-[#555] w-full py-1.5 px-4 font-medium text-xs tracking-wider text-neutral-50">
                        <span class="absolute h-0 w-0 rounded-full bg-[#444] transition-all duration-300 group-hover:h-full group-hover:w-full"></span>
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
                    <button wire:click="rotateImage" wire:loading.attr="disabled" class="group relative inline-flex items-center justify-center overflow-hidden rounded-full font-medium text-xs tracking-wider text-neutral-50 cursor-pointer bg-[#555] py-1.5 px-3">
                        <span class="absolute h-0 w-0 rounded-full bg-blue-400 transition-all duration-300 group-hover:h-56 group-hover:w-full"></span>
                        <span class="relative" wire:loading.remove wire:target="rotateImage">Apply</span>
                        <span class="relative" wire:loading wire:target="rotateImage">Processing...</span>
                    </button>
                    @if(count($editHistory))
                    <button type="button" wire:click="undoEditorChanges" class="group relative inline-flex items-center justify-center overflow-hidden rounded-full bg-[#555] w-full py-1.5 px-4 font-medium text-xs tracking-wider text-neutral-50">
                        <span class="absolute h-0 w-0 rounded-full bg-[#444] transition-all duration-300 group-hover:h-full group-hover:w-full"></span>
                        <span class="relative">Undo</span>
                    </button>
                    <button type="button" wire:click="saveEditorChanges" class="group relative inline-flex items-center justify-center overflow-hidden rounded-full bg-[#555] w-full py-1.5 px-4 font-medium text-xs tracking-wider text-neutral-50">
                        <span class="absolute h-0 w-0 rounded-full bg-[#444] transition-all duration-300 group-hover:h-full group-hover:w-full"></span>
                        <span class="relative">Save</span>
                    </button>
                    @endif
                    <button type="button" wire:click="resetForm" class="group relative inline-flex items-center justify-center overflow-hidden rounded-full bg-[#555] w-full py-1.5 px-4 font-medium text-xs tracking-wider text-neutral-50">
                        <span class="absolute h-0 w-0 rounded-full bg-[#444] transition-all duration-300 group-hover:h-full group-hover:w-full"></span>
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
                    <button wire:click="cropImage" wire:loading.attr="disabled" class="group relative inline-flex items-center justify-center overflow-hidden rounded-full font-medium text-xs tracking-wider text-neutral-50 cursor-pointer bg-[#555] py-1.5 px-3">
                        <span class="absolute h-0 w-0 rounded-full bg-blue-400 transition-all duration-300 group-hover:h-56 group-hover:w-full"></span>
                        <span class="relative" wire:loading.remove wire:target="cropImage">Apply</span>
                        <span class="relative" wire:loading wire:target="cropImage">Processing...</span>
                    </button>
                    @if(count($editHistory))
                    <button type="button" wire:click="undoEditorChanges" class="group relative inline-flex items-center justify-center overflow-hidden rounded-full bg-[#555] w-full py-1.5 px-4 font-medium text-xs tracking-wider text-neutral-50">
                        <span class="absolute h-0 w-0 rounded-full bg-[#444] transition-all duration-300 group-hover:h-full group-hover:w-full"></span>
                        <span class="relative">Undo</span>
                    </button>
                    <button type="button" wire:click="saveEditorChanges" class="group relative inline-flex items-center justify-center overflow-hidden rounded-full bg-[#555] w-full py-1.5 px-4 font-medium text-xs tracking-wider text-neutral-50">
                        <span class="absolute h-0 w-0 rounded-full bg-[#444] transition-all duration-300 group-hover:h-full group-hover:w-full"></span>
                        <span class="relative">Save</span>
                    </button>
                    @endif
                    <button type="button" wire:click="resetForm" class="group relative inline-flex items-center justify-center overflow-hidden rounded-full bg-[#555] w-full py-1.5 px-4 font-medium text-xs tracking-wider text-neutral-50">
                        <span class="absolute h-0 w-0 rounded-full bg-[#444] transition-all duration-300 group-hover:h-full group-hover:w-full"></span>
                        <span class="relative">Back</span>
                    </button>
                </div>
                @endif

                @if($selectedForm == 'image-text')
                <div class="mb-1 w-full">
                    <label for="imageText" class="inline-block text-gray-500 mb-1 text-xs font-normal">Text:</label>
                    <input type="text" class="control-input w-full" id="imageText" wire:model="imageText">
                </div>
                <div class="mb-1 w-full">
                    <label for="imageFont" class="inline-block text-gray-500 mb-1 text-xs font-normal">Font face:</label>
                    <select id="imageFont" class="block text-gray-600 border border-gray-300 w-full py-1 px-2 appearance-none rounded-md text-xs font-medium leading-5" wire:model="imageFont">
                        <option value="">Choose font:</option>
                        @foreach($this->buildFontList() as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-1 w-full">
                    <label for="imageFontSize" class="inline-block text-gray-500 mb-1 text-xs font-normal">Font size:</label>
                    <input type="text" class="control-input w-full" id="imageFontSize" wire:model="imageFontSize" />
                </div>
                <div class="mb-1 w-full">
                    <label for="imageTextColor" class="inline-block text-gray-500 mb-1 text-xs font-normal">Font color:</label>
                    <input type="color" class="control-input w-full" id="imageTextColor" wire:model="imageTextColor" />
                </div>
                <div class="mb-1 w-full">
                    <label for="imageTextAngle" class="inline-block text-gray-500 mb-1 text-xs font-normal">Font angle:</label>
                    <input type="range" class="control-input w-full" id="imageTextAngle" wire:model="imageTextAngle" min="0" max="360">
                </div>
                <div class="flex flex-col justify-start items-stretch w-full mt-2 gap-y-2">
                    <button wire:click="addText" wire:loading.attr="disabled" class="group relative inline-flex items-center justify-center overflow-hidden rounded-full font-medium text-xs tracking-wider text-neutral-50 cursor-pointer bg-[#555] py-1.5 px-3">
                        <span class="absolute h-0 w-0 rounded-full bg-blue-400 transition-all duration-300 group-hover:h-56 group-hover:w-full"></span>
                        <span class="relative" wire:loading.remove wire:target="addText">Apply</span>
                        <span class="relative" wire:loading wire:target="addText">Processing...</span>
                    </button>
                    @if(count($editHistory))
                    <button type="button" wire:click="undoEditorChanges" class="group relative inline-flex items-center justify-center overflow-hidden rounded-full bg-[#555] w-full py-1.5 px-4 font-medium text-xs tracking-wider text-neutral-50">
                        <span class="absolute h-0 w-0 rounded-full bg-[#444] transition-all duration-300 group-hover:h-full group-hover:w-full"></span>
                        <span class="relative">Undo</span>
                    </button>
                    <button type="button" wire:click="saveEditorChanges" class="group relative inline-flex items-center justify-center overflow-hidden rounded-full bg-[#555] w-full py-1.5 px-4 font-medium text-xs tracking-wider text-neutral-50">
                        <span class="absolute h-0 w-0 rounded-full bg-[#444] transition-all duration-300 group-hover:h-full group-hover:w-full"></span>
                        <span class="relative">Save</span>
                    </button>
                    @endif
                    <button type="button" wire:click="resetForm" class="group relative inline-flex items-center justify-center overflow-hidden rounded-full bg-[#555] w-full py-1.5 px-4 font-medium text-xs tracking-wider text-neutral-50">
                        <span class="absolute h-0 w-0 rounded-full bg-[#444] transition-all duration-300 group-hover:h-full group-hover:w-full"></span>
                        <span class="relative">Back</span>
                    </button>
                </div>
                @endif

            </div>
        </div>
    </div>

    <div class="bg-gray-200 h-10 min-h-10 max-h-10 xl:h-11 xl:min-h-11 xl:max-h-11 2xl:h-12 2xl:min-h-12 2xl:max-h-12 w-full">
        <div class="flex items-center justify-start px-4 h-full w-full gap-x-2"></div>
    </div>
</div>
