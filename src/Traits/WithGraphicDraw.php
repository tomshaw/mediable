<?php

namespace TomShaw\Mediable\Traits;

use Illuminate\Support\Facades\Storage;
use TomShaw\Mediable\GraphicDraw\GraphicDraw;

trait WithGraphicDraw
{
    public $flipMode = '';

    public $filterMode = '';

    public $contrast = 0;

    public $brightness = 0;

    public $colorizeRed = 0;

    public $colorizeGreen = 0;

    public $colorizeBlue = 0;

    public $smoothLevel = 0;

    public $pixelateBlockSize = 1;

    public function getFlipModes()
    {
        return GraphicDraw::getFlipModes();
    }

    public function getFilterModes()
    {
        return GraphicDraw::getFilterModes();
    }

    public function getImageSize(string $filename, array $image_info = []): array|false
    {
        return GraphicDraw::getimagesize($filename, $image_info);
    }

    public function getImageMimeType(int $imageType): ?string
    {
        return GraphicDraw::getImageMimeType($imageType);
    }

    public function getImageExtension(int $imageType): ?string
    {
        return GraphicDraw::getImageExtension($imageType);
    }

    public function flipImage()
    {
        if (! $this->flipMode) {
            return;
        }

        GraphicDraw::flipAndSave(Storage::path($this->fileDir), $this->flipMode);

        $this->generateUniqueId();
    }

    public function filterImage()
    {
        if (! $this->filterMode) {
            return;
        }

        $args = [];
        if ($this->filterMode == IMG_FILTER_CONTRAST) {
            $args[] = $this->contrast;
        } elseif ($this->filterMode == IMG_FILTER_BRIGHTNESS) {
            $args[] = $this->brightness;
        } elseif ($this->filterMode == IMG_FILTER_COLORIZE) {
            $args[] = $this->colorizeRed;
            $args[] = $this->colorizeGreen;
            $args[] = $this->colorizeBlue;
        } elseif ($this->filterMode == IMG_FILTER_SMOOTH) {
            $args[] = $this->smoothLevel;
        } elseif ($this->filterMode == IMG_FILTER_PIXELATE) {
            $args[] = $this->pixelateBlockSize;
        }

        GraphicDraw::filterAndSave(Storage::path($this->fileDir), $this->filterMode, $args);

        $this->generateUniqueId();
    }
}
