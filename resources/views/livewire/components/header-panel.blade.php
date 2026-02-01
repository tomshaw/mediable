<?php

use Livewire\Attributes\{Modelable, Reactive};
use Livewire\Component;
use TomShaw\Mediable\Concerns\ShowState;

new class extends Component {
    #[Reactive]
    public ?ShowState $show = null;

    #[Modelable]
    public string $searchTerm = '';

    public function expandModal(): void
    {
        $this->dispatch('panel:expand-modal');
    }

    public function closeModal(): void
    {
        $this->dispatch('panel:close-modal');
    }
}; ?>

<div class="flex items-center justify-between h-full px-8">

    <div class="flex items-center justify-end">
        <button class="text-[#222] w-28 cursor-pointer" wire:click="expandModal()" role="button">
            <x-icons.logo />
        </button>
    </div>

    @if($show?->isShowSearch())
    <div class="flex items-center justify-center w-full">
        <div class="md:w-72">
            <input type="text" class="control-input" wire:model.live="searchTerm" spellcheck="false" placeholder="Search">
        </div>
    </div>
    @endif

    <div class="flex items-center justify-end">
        <button class="text-[#555] focus:outline-none transform transition duration-500 hover:rotate-180" wire:click="closeModal()">
            <x-icons.exit />
        </button>
    </div>

</div>
