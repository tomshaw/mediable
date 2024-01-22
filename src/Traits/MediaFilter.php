<?php

namespace TomShaw\Mediable\Traits;

use Illuminate\Contracts\Database\Eloquent\Builder;

trait MediaFilter
{
    public function scopeSearch(Builder $query, string $term): Builder
    {
        $term = "%$term%";

        return $query->where('title', 'like', $term)->orWhere('description', 'like', $term);
    }
}
