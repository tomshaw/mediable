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

    public int $scaleWidth = 0;

    public int $scaleHeight = 0;

    public ?int $scaleMode = 4;

    public int $rotateAngle = 0;

    public string $rotateBgColor = '#000000';

    public int $cropX = 0;

    public int $cropY = 0;

    public int $cropWidth = 0;

    public int $cropHeight = 0;

    public string $imageText = '';

    public string $imageFont = '';

    public float $imageFontSize = 42.0;

    public string $imageTextColor = '#000000';

    public int $imageTextAngle = 0;

    public array $editHistory = [];

    public ?string $selectedForm = '';

    public array $availableForms = [
        'image-flip' => 'Flip Image',
        'image-scale' => 'Scale Image',
        'image-filter' => 'Filter Image',
        'image-rotate' => 'Rotate Image',
        'image-crop' => 'Crop Image',
        'image-text' => 'Add Text',
    ];

    public function setForm(string $key): void
    {
        $this->selectedForm = $key;
    }

    public function resetForm(): void
    {
        $this->selectedForm = '';
    }

    public function getFlipModes(): array
    {
        return GraphicDraw::getFlipModes();
    }

    public function getFilterModes(): array
    {
        return GraphicDraw::getFilterModes();
    }

    public function getScaleModes(): array
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

    public function getDiskImagePath(): ?string
    {
        if (! $this->attachment?->file_dir) {
            return null;
        }

        $disk = config('mediable.disk');

        return Storage::disk($disk)->path($this->attachment->file_dir);
    }

    public function updatedScaleWidth(): void
    {
        $path = $this->getDiskImagePath();
        if (! $path) {
            return;
        }

        $image = GraphicDraw::imagecreatefrompath($path);
        if ($image === false) {
            return;
        }

        $originalWidth = GraphicDraw::imagesx($image);
        $originalHeight = GraphicDraw::imagesy($image);

        if ($originalHeight === 0 || $originalHeight === false) {
            return;
        }

        $aspectRatio = $originalWidth / $originalHeight;

        $this->scaleHeight = (int) ($this->scaleWidth / $aspectRatio);
    }

    public function updatedScaleHeight(): void
    {
        $path = $this->getDiskImagePath();
        if (! $path) {
            return;
        }

        $image = GraphicDraw::imagecreatefrompath($path);
        if ($image === false) {
            return;
        }

        $originalWidth = GraphicDraw::imagesx($image);
        $originalHeight = GraphicDraw::imagesy($image);

        if ($originalHeight === 0 || $originalHeight === false) {
            return;
        }

        $aspectRatio = $originalWidth / $originalHeight;

        $this->scaleWidth = (int) ($this->scaleHeight * $aspectRatio);
    }

    public function flipImage(): void
    {
        $path = $this->getDiskImagePath();

        if (! $this->flipMode || ! $path) {
            return;
        }

        GraphicDraw::flipAndSave($path, $this->flipMode);

        $this->generateUniqueId();

        $this->editHistory[] = $this->getDrawSettings();
    }

    public function scaleImage(): void
    {
        $path = $this->getDiskImagePath();

        if (! $this->scaleMode || ! $path) {
            return;
        }

        GraphicDraw::scaleAndSave($path, $this->scaleWidth, $this->scaleHeight, $this->scaleMode);

        $this->generateUniqueId();

        $this->editHistory[] = $this->getDrawSettings();
    }

    public function filterImage(): void
    {
        $path = $this->getDiskImagePath();

        if (! $this->filterMode || ! $path) {
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

        GraphicDraw::filterAndSave($path, $this->filterMode, $args);

        $this->generateUniqueId();

        $this->editHistory[] = $this->getDrawSettings();
    }

    public function rotateImage(): void
    {
        $path = $this->getDiskImagePath();

        if (! $this->rotateAngle || ! $path) {
            return;
        }

        $backgroundColor = $this->normalizeHexValue($this->rotateBgColor);

        GraphicDraw::rotateAndSave($path, $this->rotateAngle, $backgroundColor);

        $this->generateUniqueId();

        $this->editHistory[] = $this->getDrawSettings();
    }

    public function cropImage(): void
    {
        $path = $this->getDiskImagePath();

        if (! $path || $this->cropWidth <= 0 || $this->cropHeight <= 0) {
            return;
        }

        $rect = ['x' => $this->cropX, 'y' => $this->cropY, 'width' => $this->cropWidth, 'height' => $this->cropHeight];

        GraphicDraw::cropAndSave($path, $rect);

        $this->generateUniqueId();

        $this->editHistory[] = $this->getDrawSettings();
    }

    public function addText(): void
    {
        $path = $this->getDiskImagePath();

        if (! $path || $this->imageFontSize <= 0) {
            return;
        }

        if (! file_exists($this->imageFont) || ! is_readable($this->imageFont)) {
            return;
        }

        if (trim($this->imageText) === '') {
            return;
        }

        if ($this->imageTextAngle < 0 || $this->imageTextAngle > 360) {
            return;
        }

        $color = $this->normalizeHexValue($this->imageTextColor);

        $centered = $this->centerText();
        if (! $centered) {
            return;
        }

        GraphicDraw::textAndSave($path, $this->imageFontSize, $this->imageTextAngle, $centered[0], $centered[1], $color, $this->imageFont, $this->imageText);

        $this->generateUniqueId();

        $this->editHistory[] = $this->getDrawSettings();
    }

    public function normalizeColors(): void
    {
        [$r, $g, $b] = sscanf($this->colorize, '#%02x%02x%02x');

        $this->colorizeRed = $r - 255;
        $this->colorizeGreen = $g - 255;
        $this->colorizeBlue = $b - 255;
    }

    public function normalizeHexValue(string $hexColor): int|false
    {
        $hexColor = ltrim($hexColor, '#');

        $red = (int) hexdec(substr($hexColor, 0, 2));
        $green = (int) hexdec(substr($hexColor, 2, 2));
        $blue = (int) hexdec(substr($hexColor, 4, 2));

        $image = imagecreatetruecolor(1, 1);
        $color = imagecolorallocate($image, $red, $green, $blue);
        imagedestroy($image);

        return $color;
    }

    public function centerText(): ?array
    {
        $path = $this->getDiskImagePath();
        if (! $path) {
            return null;
        }

        [$imageWidth, $imageHeight] = getimagesize($path);

        $bbox = imagettfbbox($this->imageFontSize, $this->imageTextAngle, $this->imageFont, $this->imageText);

        $textWidth = $bbox[2] - $bbox[0];
        $textHeight = $bbox[7] - $bbox[1];

        $x = ($imageWidth / 2) - ($textWidth / 2);
        $y = ($imageHeight / 2) - ($textHeight / 2);

        return [$x, $y];
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
            'scaleWidth' => $this->scaleWidth,
            'scaleHeight' => $this->scaleHeight,
            'scaleMode' => $this->scaleMode,
            'rotateAngle' => $this->rotateAngle,
            'rotateBgColor' => $this->rotateBgColor,
            'cropX' => $this->cropX,
            'cropY' => $this->cropY,
            'cropWidth' => $this->cropWidth,
            'cropHeight' => $this->cropHeight,
            'imageText' => $this->imageText,
            'imageFont' => $this->imageFont,
            'imageFontSize' => $this->imageFontSize,
            'imageTextColor' => $this->imageTextColor,
            'imageTextAngle' => $this->imageTextAngle,
            'primaryId' => $this->primaryId,
        ];
    }

    public function fillEditorProperties(): void
    {
        $this->fill([
            'flipMode' => null,
            'filterMode' => null,
            'contrast' => 0,
            'brightness' => 0,
            'colorize' => '',
            'colorizeRed' => -50,
            'colorizeGreen' => -50,
            'colorizeBlue' => 50,
            'smoothLevel' => 0,
            'pixelateBlockSize' => 1,
            'scaleWidth' => 0,
            'scaleHeight' => 0,
            'scaleMode' => 4,
            'rotateAngle' => 0,
            'rotateBgColor' => '#000000',
            'cropX' => 0,
            'cropY' => 0,
            'cropWidth' => 0,
            'cropHeight' => 0,
            'imageText' => '',
            'imageFont' => '',
            'imageFontSize' => 42.0,
            'imageTextColor' => '#000000',
            'imageTextAngle' => 0,
        ]);
    }
}
