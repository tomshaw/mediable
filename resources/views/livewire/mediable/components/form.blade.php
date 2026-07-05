<?php

use Livewire\Component;
use TomShaw\Mediable\Concerns\AttachmentState;
use TomShaw\Mediable\Eloquent\Eloquent;
use TomShaw\Mediable\Enums\BrowserEvents;
use TomShaw\Mediable\GraphicDraw\GraphicDraw;
use TomShaw\Mediable\Models\Attachment;
use TomShaw\Mediable\Traits\{WithFonts, WithGraphicDraw};

new class extends Component
{
    use WithFonts;
    use WithGraphicDraw;

    public ?AttachmentState $selectedAttachment = null;

    public ?AttachmentState $attachment = null;

    public ?int $primaryId = null;

    public int $editVersion = 0;

    public function mount(?int $activeId = null): void
    {
        if ($activeId) {
            $this->loadSelectedAttachment($activeId);
            $this->prepareImageEditor();
        }
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

        $this->dispatch(BrowserEvents::EDITOR_ATTACHMENT_UPDATED->value, id: $this->attachment->getId(), version: $this->editVersion);
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

    protected function refreshWorkingCopy(): void
    {
        if (! $this->attachment?->id) {
            return;
        }

        Attachment::whereKey($this->attachment->id)->touch();

        $item = Attachment::find($this->attachment->id);

        $this->attachment = $item ? AttachmentState::fromAttachment($item) : null;

        $this->editVersion++;

        if ($this->attachment) {
            $this->dispatch(BrowserEvents::EDITOR_ATTACHMENT_UPDATED->value, id: $this->attachment->id, version: $this->editVersion);
        }
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

        $this->dispatch(BrowserEvents::FORM_EDITOR_SAVED->value);
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

        $this->editVersion++;

        $this->initializeScaleDimensions();

        $this->dispatch(BrowserEvents::EDITOR_ATTACHMENT_UPDATED->value, id: $this->attachment->getId(), version: $this->editVersion);
    }

    public function mimeTypeImage(string $mimeType): bool
    {
        return str_starts_with($mimeType, 'image/');
    }
}; ?>

<div class="flex flex-col justify-between p-0 m-0 w-full h-full">

    <div class="h-12 min-h-12 max-h-12 w-full">
        <div class="flex items-center justify-between px-4 h-full w-full">
            <span class="text-[11px] font-medium uppercase tracking-widest text-zinc-400 dark:text-zinc-500 select-none">Image editor</span>
            <div></div>
        </div>
    </div>

    <div class="grow border-b border-t border-zinc-200 dark:border-zinc-800 scrollY h-auto p-2">
        <div class="flex items-start justify-center p-0 m-0 w-full h-full">
            <div class="flex flex-col items-start justify-start w-full p-2">

                @if ($attachment && $this->mimeTypeImage($attachment->file_type))
                <figure class="w-full mb-3">
                    <img src="{{ $attachment->file_url }}?v={{ ($attachment->updated_at ? strtotime($attachment->updated_at) : 0) }}.{{ $editVersion }}" class="w-full object-cover rounded-lg ring-1 ring-zinc-950/10 dark:ring-white/10 shadow-sm" />
                </figure>
                @endif

                @if($selectedForm == '')
                    <div class="w-full flex flex-col gap-1">
                    @foreach($availableForms as $key => $value)
                    <button wire:click="setForm('{{$key}}')" class="group flex items-center justify-between w-full h-8 rounded-lg px-2.5 text-xs font-medium text-zinc-600 hover:bg-zinc-200/70 hover:text-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-zinc-100 cursor-pointer transition-colors">
                        <span>{{ $value }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5 text-zinc-300 dark:text-zinc-600 transition-colors group-hover:text-zinc-500 dark:group-hover:text-zinc-300">
                            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 0 1 .02-1.06L11.168 10 7.23 6.29a.75.75 0 1 1 1.04-1.08l4.5 4.25a.75.75 0 0 1 0 1.08l-4.5 4.25a.75.75 0 0 1-1.06-.02Z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    @endforeach
                    </div>
                @endif

                @if($selectedForm == 'image-flip')
                    <x-mediable::form-select
                        label="Image flip"
                        id="flipMode"
                        placeholder="Flip modes"
                        :options="$this->getFlipModes()"
                        wire:model.live="flipMode"
                    />
                    <x-mediable::form-actions action="flipImage" target="flipImage" :showHistory="count($editHistory) > 0" />
                @endif

                @if($selectedForm == 'image-scale')
                    <x-mediable::form-select
                        label="Scale mode"
                        id="scaleMode"
                        placeholder="Scale modes"
                        :options="$this->getScaleModes()"
                        wire:model="scaleMode"
                        wire:change="scaleImage"
                    />
                    <x-mediable::form-input label="Width" id="scaleWidth" type="number" wire:model.live.debounce.500ms="scaleWidth" />
                    <x-mediable::form-input label="Height" id="scaleHeight" type="number" wire:model.live.debounce.500ms="scaleHeight" />
                    <x-mediable::form-actions action="scaleImage" target="scaleImage" :showHistory="count($editHistory) > 0" />
                @endif

                @if($selectedForm == 'image-filter')
                    <x-mediable::form-select
                        label="Image filters"
                        id="filterMode"
                        placeholder="Filter modes"
                        :options="$this->getFilterModes()"
                        wire:model.live="filterMode"
                    />
                    @if($filterMode == IMG_FILTER_CONTRAST)
                        <x-mediable::form-input label="Contrast" id="contrast" type="number" wire:model="contrast" min="-100" max="100" step="1" />
                    @endif
                    @if($filterMode == IMG_FILTER_BRIGHTNESS)
                        <x-mediable::form-input label="Brightness" id="brightness" type="number" wire:model="brightness" min="-255" max="255" step="1" />
                    @endif
                    @if($filterMode == IMG_FILTER_COLORIZE)
                        <x-mediable::form-input label="Colorize color" id="colorize" type="color" wire:model="colorize" />
                    @endif
                    @if($filterMode == IMG_FILTER_SMOOTH)
                        <x-mediable::form-input label="Smooth level" id="smoothLevel" type="number" wire:model="smoothLevel" min="-10" max="10" step="1" />
                    @endif
                    @if($filterMode == IMG_FILTER_PIXELATE)
                        <x-mediable::form-input label="Pixelate block size" id="pixelateBlockSize" type="number" wire:model="pixelateBlockSize" min="1" step="1" />
                    @endif
                    <x-mediable::form-actions action="filterImage" target="filterImage" :showHistory="count($editHistory) > 0" />
                @endif

                @if($selectedForm == 'image-rotate')
                    <x-mediable::form-input label="Rotation (degrees)" id="rotateAngle" type="range" wire:model="rotateAngle" min="0" max="360" />
                    <x-mediable::form-input label="Background color" id="rotateBgColor" type="color" wire:model="rotateBgColor" />
                    <x-mediable::form-checkbox label="Ignore transparent" id="rotateIgnoreTransparent" wire:model="rotateIgnoreTransparent" />
                    <x-mediable::form-actions action="rotateImage" target="rotateImage" :showHistory="count($editHistory) > 0" />
                @endif

                @if($selectedForm == 'image-crop')
                    <x-mediable::form-input label="X coordinate" id="cropX" type="number" wire:model="cropX" />
                    <x-mediable::form-input label="Y coordinate" id="cropY" type="number" wire:model="cropY" />
                    <x-mediable::form-input label="Width" id="cropWidth" type="number" wire:model="cropWidth" />
                    <x-mediable::form-input label="Height" id="cropHeight" type="number" wire:model="cropHeight" />
                    <x-mediable::form-actions action="cropImage" target="cropImage" :showHistory="count($editHistory) > 0" />
                @endif

                @if($selectedForm == 'image-text')
                    <x-mediable::form-input label="Text" id="imageText" type="text" wire:model="imageText" />
                    <x-mediable::form-select
                        label="Font face"
                        id="imageFont"
                        placeholder="Choose font"
                        :options="$this->buildFontList()"
                        wire:model="imageFont"
                    />
                    <x-mediable::form-input label="Font size" id="imageFontSize" type="text" wire:model="imageFontSize" />
                    <x-mediable::form-input label="Font color" id="imageTextColor" type="color" wire:model="imageTextColor" />
                    <x-mediable::form-input label="Font angle" id="imageTextAngle" type="range" wire:model="imageTextAngle" min="0" max="360" />
                    <x-mediable::form-actions action="addText" target="addText" :showHistory="count($editHistory) > 0" />
                @endif

            </div>
        </div>
    </div>

    <div class="h-12 min-h-12 max-h-12 w-full">
        <div class="flex items-center justify-start px-4 h-full w-full gap-x-2"></div>
    </div>
</div>
