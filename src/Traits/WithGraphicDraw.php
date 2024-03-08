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

    public $colorize;

    public $colorizeRed = -50;

    public $colorizeGreen = -50;

    public $colorizeBlue = 50;

    public $smoothLevel = 0;

    public $pixelateBlockSize = 1;

    public $newWidth = 100;

    public $newHeight = -1;

    public $scaleMode = '';

    public function getFlipModes()
    {
        return GraphicDraw::getFlipModes();
    }

    public function getFilterModes()
    {
        return GraphicDraw::getFilterModes();
    }

    public function getScaleModes()
    {
        return GraphicDraw::getScaleModes();
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

        GraphicDraw::flipAndSave(Storage::path($this->attachment->file_dir), $this->flipMode);

        $this->generateUniqueId();
    }

    public function scaleImage()
    {
        if (! $this->scaleMode) {
            return;
        }

        GraphicDraw::scaleAndSave(Storage::path($this->attachment->file_dir), $this->newWidth, $this->newHeight, $this->scaleMode);

        $this->generateUniqueId();
    }

    public function filterImage()
    {
        if (! $this->filterMode) {
            return;
        }

        $this->normalizeColors();

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

        GraphicDraw::filterAndSave(Storage::path($this->attachment->file_dir), $this->filterMode, $args);

        $this->generateUniqueId();
    }

    public function normalizeColors()
    {
        [$r, $g, $b] = sscanf($this->colorize, '#%02x%02x%02x');

        $this->colorizeRed = $r - 255;
        $this->colorizeGreen = $g - 255;
        $this->colorizeBlue = $b - 255;
    }
}
