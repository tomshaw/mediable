<?php

namespace TomShaw\Mediable\Eloquent;

use Illuminate\Support\Facades\Facade;

class Eloquent extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return EloquentManager::class;
    }
}
