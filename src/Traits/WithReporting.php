<?php

namespace TomShaw\Mediable\Traits;

use TomShaw\Mediable\Eloquent\Eloquent;

trait WithReporting
{
    public function getMimeTypeStatsProperty(): \Illuminate\Database\Eloquent\Collection
    {
        return Eloquent::getMimeTypeStats();
    }

    public function getMimeTypeTotalsProperty(): ?\TomShaw\Mediable\Models\Attachment
    {
        return Eloquent::getMimeTypeTotals();
    }
}
