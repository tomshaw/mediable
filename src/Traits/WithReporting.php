<?php

namespace TomShaw\Mediable\Traits;

use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use TomShaw\Mediable\Eloquent\Eloquent;
use TomShaw\Mediable\Models\Attachment;

trait WithReporting
{
    /**
     * @return Collection<int, Attachment>
     */
    #[Computed]
    public function mimeTypeStats(): Collection
    {
        return Eloquent::getMimeTypeStats();
    }

    #[Computed]
    public function mimeTypeTotals(): ?Attachment
    {
        return Eloquent::getMimeTypeTotals();
    }
}
