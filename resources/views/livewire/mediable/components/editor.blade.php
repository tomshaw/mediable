<?php

use Livewire\Attributes\On;
use Livewire\Component;
use TomShaw\Mediable\Concerns\AttachmentState;
use TomShaw\Mediable\Enums\BrowserEvents;
use TomShaw\Mediable\Models\Attachment;

new class extends Component
{
    public ?AttachmentState $attachment = null;

    public string $uniqueId = '';

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
    }

    #[On(BrowserEvents::EDITOR_ATTACHMENT_UPDATED->value)]
    public function handleAttachmentUpdated(int $id): void
    {
        $this->loadAttachment($id);
    }

    #[On(BrowserEvents::PANEL_UNIQUE_ID_UPDATED->value)]
    public function handleUniqueIdUpdated(string $uniqueId): void
    {
        $this->uniqueId = $uniqueId;
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
}; ?>

<div class="relative flex items-center justify-center overflow-hidden p-4 md:p-6 lg:p-8 m-0 h-full bg-pattern">
    <div class="absolute top-0 left-0 z-10 bg-black/80 text-white text-xs px-3 py-2 rounded-br-lg font-mono leading-relaxed max-w-md">
        <div class="text-yellow-300 font-bold mb-1">EDITOR</div>
        @if ($attachment)
        <div>ID: {{ $attachment->id }}</div>
        <div>Name: {{ $attachment->file_name }}</div>
        <div>Dir: {{ $attachment->file_dir }}</div>
        <div>UniqueId: {{ $uniqueId }}</div>
        @else
        <div class="text-red-400">No attachment loaded</div>
        @endif
    </div>

    @if ($attachment && $this->mimeTypeImage($attachment->file_type))
    <div class="flex items-center justify-center">
        <img src="{{ asset($attachment->file_url) }}?id={{ $uniqueId }}" class="object-contain shadow-md max-h-96">
    </div>
    @endif
</div>
