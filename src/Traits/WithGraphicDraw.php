<?php

namespace TomShaw\Mediable\Traits;

use Illuminate\Support\Facades\Storage;
use TomShaw\Mediable\GraphicDraw\GraphicDraw;

trait WithGraphicDraw
{
    public $flipMode = IMG_FLIP_HORIZONTAL;

    public function getFlipModes()
    {
        return GraphicDraw::getFlipModes();
    }

    public function flipImage()
    {
        GraphicDraw::flipAndSave(Storage::path($this->fileDir), $this->flipMode);

        $this->generateUniqueId();
    }
}
