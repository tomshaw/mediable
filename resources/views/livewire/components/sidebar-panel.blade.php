<?php

use Livewire\Attributes\Reactive;
use Livewire\Component;
use TomShaw\Mediable\Concerns\AttachmentState;

new class extends Component {
    #[Reactive]
    public ?AttachmentState $attachment = null;

    public string $title = '';

    public string $caption = '';

    public int $sort_order = 0;

    public string $styles = '';

    public string $description = '';

    public function mount(): void
    {
        $this->syncFormFromAttachment();
    }

    public function updatedAttachment(): void
    {
        $this->syncFormFromAttachment();
    }

    protected function syncFormFromAttachment(): void
    {
        if ($this->attachment) {
            $this->title = $this->attachment->title ?? '';
            $this->caption = $this->attachment->caption ?? '';
            $this->sort_order = $this->attachment->sort_order ?? 0;
            $this->styles = $this->attachment->styles ?? '';
            $this->description = $this->attachment->description ?? '';
        }
    }

    public function updateAttachment(): void
    {
        $this->dispatch('panel:update-attachment', data: [
            'title' => $this->title,
            'caption' => $this->caption,
            'sort_order' => $this->sort_order,
            'styles' => $this->styles,
            'description' => $this->description,
        ]);
    }
}; ?>

<div class="flex flex-col justify-between p-0 m-0 w-full h-full">

    <div class="bg-gray-200 h-10 min-h-10 max-h-10 xl:h-11 xl:min-h-11 xl:max-h-11 2xl:h-12 2xl:min-h-12 2xl:max-h-12 w-full">
        <div class="flex items-center justify-between px-4 h-full w-full">
            <div></div>
            <div></div>
        </div>
    </div>

    <div class="flex-grow border-b border-t border-[#ccc] scrollY h-auto p-2">
        <div class="flex items-start justify-center p-0 m-0 w-full h-full">
            <form class="w-full" wire:submit.prevent="updateAttachment" role="form">
                <div class="p-2 m-0">
                    <div class="mb-1">
                        <label class="inline-block mb-1 font-medium text-xs tracking-wider text-neutral-600">Title</label>
                        <input type="text" class="control-input" wire:model="title" spellcheck="false">
                    </div>
                    <div class="mb-1">
                        <label class="inline-block mb-1 font-medium text-xs tracking-wider text-neutral-600">Caption</label>
                        <input type="text" class="control-input" wire:model="caption" spellcheck="false">
                    </div>
                    <div class="mb-1">
                        <label class="inline-block mb-1 font-medium text-xs tracking-wider text-neutral-600">Order</label>
                        <input type="text" class="control-input" wire:model="sort_order" spellcheck="false">
                    </div>
                    <div class="mb-1">
                        <label class="inline-block mb-1 font-medium text-xs tracking-wider text-neutral-600">Styles</label>
                        <input type="text" class="control-input" wire:model="styles" spellcheck="false">
                    </div>
                    <div class="mb-1">
                        <label class="inline-block mb-1 font-medium text-xs tracking-wider text-neutral-600">Description</label>
                        <textarea class="control-input focus:ring-0" wire:model="description" rows="4" spellcheck="false"></textarea>
                    </div>
                </div>
                <div class="flex flex-col items-start justify-start flex-nowrap gap-y-2 p-0 mt-1">
                    <button type="reset" class="group relative inline-flex items-center justify-center overflow-hidden rounded-full bg-[#555] w-full py-1.5 px-4 font-medium text-xs tracking-wider text-neutral-50">
                        <span class="absolute h-0 w-0 rounded-full bg-rose-400 transition-all duration-300 group-hover:h-full group-hover:w-full"></span>
                        <span class="relative">Reset</span>
                    </button>
                    <button type="button" class="group relative inline-flex items-center justify-center overflow-hidden rounded-full bg-[#555] w-full py-1.5 px-4 font-medium text-xs tracking-wider text-neutral-50" wire:click="updateAttachment" wire:loading.attr="disabled">
                        <span class="absolute h-0 w-0 rounded-full bg-blue-400 transition-all duration-300 group-hover:h-full group-hover:w-full"></span>
                        <span class="relative" wire:loading.remove wire:target="updateAttachment">Submit</span>
                        <span class="relative" wire:loading wire:target="updateAttachment">Saving...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="bg-gray-200 h-10 min-h-10 max-h-10 xl:h-11 xl:min-h-11 xl:max-h-11 2xl:h-12 2xl:min-h-12 2xl:max-h-12 w-full">
        <div class="flex items-center justify-start px-4 h-full w-full gap-x-2"></div>
    </div>
</div>
