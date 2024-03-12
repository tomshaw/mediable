<?php

namespace TomShaw\Mediable\Traits;

use TomShaw\Mediable\Eloquent\Eloquent;

trait WithReporting
{
    public function getMimeTypeStatsProperty()
    {
        return Eloquent::getMimeTypeStats();
    }

    public function getMimeTypeTotalsProperty()
    {
        return Eloquent::getMimeTypeTotals();
    }
}
