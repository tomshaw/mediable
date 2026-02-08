<?php

use Livewire\Attributes\{On, Reactive};
use Livewire\Component;
use TomShaw\Mediable\Concerns\AttachmentState;
use TomShaw\Mediable\Eloquent\Eloquent;
use TomShaw\Mediable\Enums\BrowserEvents;
use TomShaw\Mediable\GraphicDraw\GraphicDraw;
use TomShaw\Mediable\Models\Attachment;
use TomShaw\Mediable\Traits\{WithFileSize, WithMimeTypes};

new class extends Component
{
    use WithFileSize;
    use WithMimeTypes;

    public ?AttachmentState $attachment = null;

    #[Reactive]
    public ?string $uniqueId = null;

    public int $imageWidth = 0;

    public int $imageHeight = 0;

    #[On(BrowserEvents::ATTACHMENTS_SELECTION_CHANGED->value)]
    public function handleSelectionChanged(array $selectedIds, ?int $activeId): void
    {
        $this->loadAttachment($activeId);
    }

    #[On(BrowserEvents::ATTACHMENT_ACTIVE_CHANGED->value)]
    public function handleActiveAttachmentChanged(int $id): void
    {
        $this->loadAttachment($id);
    }

    #[On(BrowserEvents::ATTACHMENT_ACTIVE_CLEARED->value)]
    public function handleActiveAttachmentCleared(): void
    {
        $this->attachment = null;
        $this->imageWidth = 0;
        $this->imageHeight = 0;
    }

    protected function loadAttachment(?int $id): void
    {
        if ($id) {
            $item = Attachment::find($id);
            if ($item) {
                $this->attachment = AttachmentState::fromAttachment($item);
                $this->calculateImageDimensions();

                return;
            }
        }

        $this->attachment = null;
        $this->imageWidth = 0;
        $this->imageHeight = 0;
    }

    protected function calculateImageDimensions(): void
    {
        $this->imageWidth = 0;
        $this->imageHeight = 0;

        if (! $this->attachment?->file_type || ! $this->mimeTypeImage($this->attachment->file_type)) {
            return;
        }

        $filePath = Eloquent::getFilePath($this->attachment->file_dir);

        if (! file_exists($filePath)) {
            return;
        }

        [$width, $height, $type] = GraphicDraw::getimagesize($filePath);

        if ($type) {
            $this->imageWidth = $width;
            $this->imageHeight = $height;
        }
    }
}; ?>

<div class="flex flex-col justify-between p-0 m-0 w-full h-full">

    <div class="bg-gray-200 h-10 min-h-10 max-h-10 xl:h-11 xl:min-h-11 xl:max-h-11 2xl:h-12 2xl:min-h-12 2xl:max-h-12 w-full">
        <div class="flex items-center justify-between px-4 h-full w-full">
            <div></div>
            <div></div>
        </div>
    </div>

    <div class="grow border-b border-t border-gray-300 scrollY h-auto p-2">
        <div class="flex items-start justify-center p-0 m-0 w-full h-full">
            <div class="flex flex-col items-start justify-start w-full p-3 gap-y-1.5">

                @if ($attachment && $this->mimeTypeImage($attachment->file_type))
                <figure class="w-full mb-0">
                    <img src="{{ $attachment->file_url }}?id={{ $uniqueId }}" class="w-full object-cover shadow rounded" />
                    <figcaption class="rounded font-mono text-xs tracking-wider text-neutral-50 overflow-hidden w-full mt-3 py-1.5 px-2 bg-neutral-900 hover:bg-neutral-950 cursor-copy" data-textcopy>{{$attachment->title}}</figcaption>
                </figure>
                @endif

                @if ($attachment && $this->mimeTypeVideo($attachment->file_type))
                <figure class="w-full mb-0">
                    <video src="{{ asset($attachment->file_url) }}" controls></video>
                    <figcaption class="rounded font-mono text-xs tracking-wider text-neutral-50 overflow-hidden w-full mt-3 py-1.5 px-2 bg-neutral-900 hover:bg-neutral-950 cursor-copy" data-textcopy>{{$attachment->title}}</figcaption>
                </figure>
                @endif

                @if ($attachment && $this->mimeTypeAudio($attachment->file_type))
                <figure class="w-full mb-0">
                    <audio controls class="w-55.75 mb-2">
                        <source src="{{ asset($attachment->file_url) }}" type="{{ $attachment->file_type }}">
                    </audio>
                    <figcaption class="rounded font-mono text-xs tracking-wider text-neutral-50 overflow-hidden w-full mt-3 py-1.5 px-2 bg-neutral-900 hover:bg-neutral-950 cursor-copy" data-textcopy>{{$attachment->title}}</figcaption>
                </figure>
                @endif

                @if ($attachment?->file_original_name)
                <div class="rounded font-mono text-xs tracking-wider text-neutral-50 overflow-hidden w-full py-1.5 px-2 bg-neutral-900 hover:bg-neutral-950 cursor-copy" data-textcopy>
                    {{ $attachment->file_original_name }}
                </div>
                @endif

                @if ($attachment?->file_size)
                <div class="rounded font-mono text-xs tracking-wider text-neutral-50 overflow-hidden w-full py-1.5 px-2 bg-neutral-900 hover:bg-neutral-950 cursor-copy" data-textcopy>
                    {{ $this->formatBytes($attachment->file_size) }}
                </div>
                @endif

                @if ($attachment?->file_type)
                <div class="rounded font-mono text-xs tracking-wider text-neutral-50 overflow-hidden w-full py-1.5 px-2 bg-neutral-900 hover:bg-neutral-950 cursor-copy" data-textcopy>
                    {{ $this->formatMimeType($attachment->file_type) }}
                </div>
                @endif

                @if ($attachment?->file_size && $imageWidth && $imageHeight)
                <div class="rounded font-mono text-xs tracking-wider text-neutral-50 overflow-hidden w-full py-1.5 px-2 bg-neutral-900 hover:bg-neutral-950 cursor-copy" data-textcopy>
                    {{ $imageWidth }}&times;{{ $imageHeight }}
                </div>
                @endif

                @if ($attachment?->created_at)
                <div class="rounded font-mono text-xs tracking-wider text-neutral-50 overflow-hidden w-full py-1.5 px-2 bg-neutral-900 hover:bg-neutral-950 cursor-copy" data-textcopy>
                    {{ $attachment->getCreatedAt() }}
                </div>
                @endif

                @if ($attachment?->updated_at)
                <div class="rounded font-mono text-xs tracking-wider text-neutral-50 overflow-hidden w-full py-1.5 px-2 bg-neutral-900 hover:bg-neutral-950 cursor-copy" data-textcopy>
                    {{ $attachment->getUpdatedAt() }}
                </div>
                @endif

            </div>
        </div>
    </div>

    <div class="bg-gray-200 h-10 min-h-10 max-h-10 xl:h-11 xl:min-h-11 xl:max-h-11 2xl:h-12 2xl:min-h-12 2xl:max-h-12 w-full">
        <div class="flex items-center justify-start px-4 h-full w-full gap-x-2"></div>
    </div>
</div>
