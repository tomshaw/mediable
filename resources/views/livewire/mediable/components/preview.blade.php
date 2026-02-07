<?php

use Livewire\Attributes\{On, Reactive};
use Livewire\Component;
use TomShaw\Mediable\Concerns\AttachmentState;
use TomShaw\Mediable\Models\Attachment;

new class extends Component
{
    public ?AttachmentState $attachment = null;

    #[Reactive]
    public string $uniqueId;

    #[On('attachments:selection-changed')]
    public function handleSelectionChanged(array $selectedIds, ?int $activeId): void
    {
        $this->loadAttachment($activeId);
    }

    #[On('attachment:active-changed')]
    public function handleActiveAttachmentChanged(int $id): void
    {
        $this->loadAttachment($id);
    }

    #[On('attachment:active-cleared')]
    public function handleActiveAttachmentCleared(): void
    {
        $this->attachment = null;
    }

    protected function loadAttachment(?int $id): void
    {
        if ($id) {
            $item = Attachment::find($id);
            if ($item) {
                $this->attachment = AttachmentState::fromAttachment($item);

                return;
            }
        }

        $this->attachment = null;
    }

    public function mimeTypeImage(string $mimeType): bool
    {
        return str_starts_with($mimeType, 'image/');
    }

    public function mimeTypeVideo(string $mimeType): bool
    {
        return str_starts_with($mimeType, 'video/');
    }

    public function mimeTypeAudio(string $mimeType): bool
    {
        return str_starts_with($mimeType, 'audio/');
    }
}; ?>

<div @class([
    "flex items-center justify-center overflow-hidden p-4 md:p-6 lg:p-8 m-0",
    ($attachment && $this->mimeTypeImage($attachment->file_type)) ? 'h-auto' : 'h-full'
])>
    @if ($attachment)
        @if ($this->mimeTypeImage($attachment->file_type))
        <div class="flex items-center justify-center h-full w-full">
            <img src="{{ asset($attachment->file_url) }}?id={{ $uniqueId }}" class="object-contain shadow-md" data-id="{{$attachment->id}}">
        </div>
        @elseif ($this->mimeTypeVideo($attachment->file_type))
        <div class="flex items-center justify-center h-full w-full">
            <video src="{{ asset($attachment->file_url) }}" controls></video>
        </div>
        @elseif ($this->mimeTypeAudio($attachment->file_type))
        <div class="flex items-center justify-center h-full w-full">
            <audio controls>
                <source src="{{ asset($attachment->file_url) }}" type="{{ $attachment->file_type }}">
            </audio>
        </div>
        @endif
    @endif
</div>
