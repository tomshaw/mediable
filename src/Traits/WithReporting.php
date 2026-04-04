<?php

namespace TomShaw\Mediable\Traits;

use Illuminate\Database\Eloquent\Collection;
use TomShaw\Mediable\Eloquent\Eloquent;
use TomShaw\Mediable\Models\Attachment;

trait WithReporting
{
    public function getMimeTypeStatsProperty(): Collection
    {
        return Eloquent::getMimeTypeStats();
    }

    public function getMimeTypeTotalsProperty(): ?Attachment
    {
        return Eloquent::getMimeTypeTotals();
    }
}
