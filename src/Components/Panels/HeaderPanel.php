<?php

namespace TomShaw\Mediable\Components\Panels;

use Livewire\Attributes\{Modelable, Reactive};
use Livewire\Component;
use TomShaw\Mediable\Concerns\ShowState;

class HeaderPanel extends Component
{
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

    public function render()
    {
        return view('mediable::livewire.components.header-panel');
    }
}
