<?php

use Livewire\Attributes\On;
use Livewire\Component;
use TomShaw\Mediable\Concerns\AttachmentState;
use TomShaw\Mediable\Eloquent\Eloquent;
use TomShaw\Mediable\GraphicDraw\GraphicDraw;
use TomShaw\Mediable\Models\Attachment;
use TomShaw\Mediable\Traits\{WithFonts, WithGraphicDraw};

new class extends Component {
    use WithFonts;
    use WithGraphicDraw;

    public ?AttachmentState $selectedAttachment = null;

    public ?AttachmentState $attachment = null;

    public ?int $primaryId = null;

    public string $uniqueId = '';

    public function mount(string $uniqueId = ''): void
    {
        $this->uniqueId = $uniqueId;
        $this->dispatch('form:request-active-id');
    }

    #[On('form:receive-active-id')]
    public function handleReceiveActiveId(int $id): void
    {
        $this->loadSelectedAttachment($id);
        $this->prepareImageEditor();
    }

    #[On('attachments:selection-changed')]
    public function handleSelectionChanged(array $selectedIds, ?int $activeId): void
    {
        $this->loadSelectedAttachment($activeId);
    }

    #[On('attachment:active-changed')]
    public function handleActiveAttachmentChanged(int $id): void
    {
        $this->loadSelectedAttachment($id);
    }

    #[On('attachment:active-cleared')]
    public function handleActiveAttachmentCleared(): void
    {
        $this->selectedAttachment = null;
    }

    protected function loadSelectedAttachment(?int $id): void
    {
        if ($id) {
            $item = Attachment::find($id);
            if ($item) {
                $this->selectedAttachment = AttachmentState::fromAttachment($item);
                return;
            }
        }

        $this->selectedAttachment = null;
    }


    protected function prepareImageEditor(): void
    {
        if (! $this->selectedAttachment) {
            return;
        }

        $originalId = $this->selectedAttachment->getId();
        $source = $this->selectedAttachment->getFileDir();
        $destination = Eloquent::randomizeName($source);
        $destination = Eloquent::copyImageFromTo($source, $destination);

        $item = Eloquent::saveImageToDatabase($this->selectedAttachment, $destination);

        $this->attachment = AttachmentState::fromAttachment($item);
        $this->primaryId = $originalId;

        $this->initializeScaleDimensions();

        $this->dispatch('editor:attachment-updated', id: $this->attachment->getId());
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

    #[On('toolbar:close-image-editor')]
    public function handleEditorClosed(): void
    {
        $this->primaryId = null;
        $this->attachment = null;
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
        if (! $this->attachment?->id) {
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

        $this->dispatch('editor:attachment-updated', id: $this->attachment->getId());
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
                    <button wire:click="setForm('{{$key}}')" class="group relative inline-flex items-center justify-center bg-[#555] hover:bg-[#444] rounded-full select-none appearance-none overflow-hidden h-7 w-full mb-1.5 font-medium text-xs tracking-wider text-neutral-50 cursor-pointer">
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
                    <x-mediable::form-select
                        label="Image Flip:"
                        id="flipMode"
                        placeholder="Flip Modes"
                        :options="$this->getFlipModes()"
                        wire:model.live="flipMode"
                    />
                    <x-mediable::form-actions action="flipImage" target="flipImage" :showHistory="count($editHistory) > 0" />
                @endif

                @if($selectedForm == 'image-scale')
                    <x-mediable::form-select
                        label="Scale mode:"
                        id="scaleMode"
                        placeholder="Scale Modes"
                        :options="$this->getScaleModes()"
                        wire:model="scaleMode"
                        wire:change="scaleImage"
                    />
                    <x-mediable::form-field label="Width:" id="scaleWidth" type="number" wire:model.live.debounce.500ms="scaleWidth" />
                    <x-mediable::form-field label="Height:" id="scaleHeight" type="number" wire:model.live.debounce.500ms="scaleHeight" />
                    <x-mediable::form-actions action="scaleImage" target="scaleImage" :showHistory="count($editHistory) > 0" />
                @endif

                @if($selectedForm == 'image-filter')
                    <x-mediable::form-select
                        label="Image Filters:"
                        id="filterMode"
                        placeholder="Filter Modes"
                        :options="$this->getFilterModes()"
                        wire:model.live="filterMode"
                    />
                    @if($filterMode == IMG_FILTER_CONTRAST)
                        <x-mediable::form-field label="Contrast:" id="contrast" type="number" wire:model="contrast" min="-100" max="100" step="1" />
                    @endif
                    @if($filterMode == IMG_FILTER_BRIGHTNESS)
                        <x-mediable::form-field label="Brightness:" id="brightness" type="number" wire:model="brightness" min="-255" max="255" step="1" />
                    @endif
                    @if($filterMode == IMG_FILTER_COLORIZE)
                        <x-mediable::form-field label="Colorize Color:" id="colorize" type="color" wire:model="colorize" />
                    @endif
                    @if($filterMode == IMG_FILTER_SMOOTH)
                        <x-mediable::form-field label="Smooth Level:" id="smoothLevel" type="number" wire:model="smoothLevel" min="-10" max="10" step="1" />
                    @endif
                    @if($filterMode == IMG_FILTER_PIXELATE)
                        <x-mediable::form-field label="Pixelate Block Size:" id="pixelateBlockSize" type="number" wire:model="pixelateBlockSize" min="1" step="1" />
                    @endif
                    <x-mediable::form-actions action="filterImage" target="filterImage" :showHistory="count($editHistory) > 0" />
                @endif

                @if($selectedForm == 'image-rotate')
                    <x-mediable::form-field label="Enter rotation amount (in degrees):" id="rotateAngle" type="range" wire:model="rotateAngle" min="0" max="360" />
                    <x-mediable::form-field label="Background Color:" id="rotateBgColor" type="color" wire:model="rotateBgColor" />
                    <x-mediable::form-field label="Ignore Transparent:" id="rotateIgnoreTransparent" type="checkbox" wire:model="rotateIgnoreTransparent" />
                    <x-mediable::form-actions action="rotateImage" target="rotateImage" :showHistory="count($editHistory) > 0" />
                @endif

                @if($selectedForm == 'image-crop')
                    <x-mediable::form-field label="X Coordinate:" id="cropX" type="number" wire:model="cropX" />
                    <x-mediable::form-field label="Y Coordinate:" id="cropY" type="number" wire:model="cropY" />
                    <x-mediable::form-field label="Width:" id="cropWidth" type="number" wire:model="cropWidth" />
                    <x-mediable::form-field label="Height:" id="cropHeight" type="number" wire:model="cropHeight" />
                    <x-mediable::form-actions action="cropImage" target="cropImage" :showHistory="count($editHistory) > 0" />
                @endif

                @if($selectedForm == 'image-text')
                    <x-mediable::form-field label="Text:" id="imageText" type="text" wire:model="imageText" />
                    <x-mediable::form-select
                        label="Font face:"
                        id="imageFont"
                        placeholder="Choose font:"
                        :options="$this->buildFontList()"
                        wire:model="imageFont"
                    />
                    <x-mediable::form-field label="Font size:" id="imageFontSize" type="text" wire:model="imageFontSize" />
                    <x-mediable::form-field label="Font color:" id="imageTextColor" type="color" wire:model="imageTextColor" />
                    <x-mediable::form-field label="Font angle:" id="imageTextAngle" type="range" wire:model="imageTextAngle" min="0" max="360" />
                    <x-mediable::form-actions action="addText" target="addText" :showHistory="count($editHistory) > 0" />
                @endif

            </div>
        </div>
    </div>

    <div class="bg-gray-200 h-10 min-h-10 max-h-10 xl:h-11 xl:min-h-11 xl:max-h-11 2xl:h-12 2xl:min-h-12 2xl:max-h-12 w-full">
        <div class="flex items-center justify-start px-4 h-full w-full gap-x-2"></div>
    </div>
</div>
