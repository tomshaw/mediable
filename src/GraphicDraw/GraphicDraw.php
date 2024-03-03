<?php

namespace TomShaw\Mediable\GraphicDraw;

use Illuminate\Support\Facades\Facade;

class GraphicDraw extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return GraphicDrawManager::class;
    }
}
