<?php

namespace TomShaw\Mediable\Traits;

use Illuminate\Support\Facades\Storage;
use TomShaw\Mediable\GraphicDraw\GraphicDraw;

trait WithGraphicDraw
{
    public ?int $flipMode = null;

    public ?int $filterMode = null;

    public int $contrast = 0;

    public int $brightness = 0;

    public ?string $colorize = '';

    public int $colorizeRed = -50;

    public int $colorizeGreen = -50;

    public int $colorizeBlue = 50;

    public int $smoothLevel = 0;

    public int $pixelateBlockSize = 1;

    public int $newWidth = 100;

    public int $newHeight = -1;

    public ?int $scaleMode = null;

    public ?int $primaryId = null;

    public array $editHistory = [];

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

        $this->editHistory[] = $this->getDrawSettings();
    }

    public function scaleImage()
    {
        if (! $this->scaleMode) {
            return;
        }

        GraphicDraw::scaleAndSave(Storage::path($this->attachment->file_dir), $this->newWidth, $this->newHeight, $this->scaleMode);

        $this->generateUniqueId();

        $this->editHistory[] = $this->getDrawSettings();
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

        $this->editHistory[] = $this->getDrawSettings();
    }

    public function normalizeColors()
    {
        [$r, $g, $b] = sscanf($this->colorize, '#%02x%02x%02x');

        $this->colorizeRed = $r - 255;
        $this->colorizeGreen = $g - 255;
        $this->colorizeBlue = $b - 255;
    }

    public function getDrawSettings(): array
    {
        return [
            'flipMode' => $this->flipMode,
            'filterMode' => $this->filterMode,
            'contrast' => $this->contrast,
            'brightness' => $this->brightness,
            'colorize' => $this->colorize,
            'colorizeRed' => $this->colorizeRed,
            'colorizeGreen' => $this->colorizeGreen,
            'colorizeBlue' => $this->colorizeBlue,
            'smoothLevel' => $this->smoothLevel,
            'pixelateBlockSize' => $this->pixelateBlockSize,
            'newWidth' => $this->newWidth,
            'newHeight' => $this->newHeight,
            'scaleMode' => $this->scaleMode,
            'primaryId' => $this->primaryId,
        ];
    }
}
