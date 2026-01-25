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

    public ?int $primaryId = null;

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

    public function getDiskImagePath()
    {
        $disk = config('mediable.disk');

        return Storage::disk($disk)->path($this->attachment->file_dir);
    }

    public function updatedScaleWidth()
    {
        $image = GraphicDraw::imagecreatefrompath($this->getDiskImagePath());

        $originalWidth = GraphicDraw::imagesx($image);
        $originalHeight = GraphicDraw::imagesy($image);

        $aspectRatio = $originalWidth / $originalHeight;

        $this->scaleHeight = intval($this->scaleWidth / $aspectRatio);
    }

    public function updatedScaleHeight()
    {
        $image = GraphicDraw::imagecreatefrompath($this->getDiskImagePath());

        $originalWidth = GraphicDraw::imagesx($image);
        $originalHeight = GraphicDraw::imagesy($image);

        $aspectRatio = $originalWidth / $originalHeight;

        $this->scaleWidth = intval($this->scaleHeight * $aspectRatio);
    }

    public function flipImage()
    {
        if (! $this->flipMode) {
            return;
        }

        GraphicDraw::flipAndSave($this->getDiskImagePath(), $this->flipMode);

        $this->generateUniqueId();

        $this->editHistory[] = $this->getDrawSettings();
    }

    public function scaleImage()
    {
        if (! $this->scaleMode) {
            return;
        }

        GraphicDraw::scaleAndSave($this->getDiskImagePath(), $this->scaleWidth, $this->scaleHeight, $this->scaleMode);

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

        GraphicDraw::filterAndSave($this->getDiskImagePath(), $this->filterMode, $args);

        $this->generateUniqueId();

        $this->editHistory[] = $this->getDrawSettings();
    }

    public function rotateImage()
    {
        if (! $this->rotateAngle) {
            return;
        }

        $backgroundColor = $this->normalizeHexValue($this->rotateBgColor);

        GraphicDraw::rotateAndSave($this->getDiskImagePath(), $this->rotateAngle, $backgroundColor);

        $this->generateUniqueId();

        $this->editHistory[] = $this->getDrawSettings();
    }

    public function cropImage()
    {
        $rect = ['x' => $this->cropX, 'y' => $this->cropY, 'width' => $this->cropWidth, 'height' => $this->cropHeight];

        GraphicDraw::cropAndSave($this->getDiskImagePath(), $rect);

        $this->generateUniqueId();

        $this->editHistory[] = $this->getDrawSettings();
    }

    public function addText()
    {
        if ($this->imageFontSize <= 0) {
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

        GraphicDraw::textAndSave($this->getDiskImagePath(), $this->imageFontSize, $this->imageTextAngle, $centered[0], $centered[1], $color, $this->imageFont, $this->imageText);

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

    public function normalizeHexValue(string $hexColor)
    {
        $hexColor = ltrim($hexColor, '#');

        $red = hexdec(substr($hexColor, 0, 2));
        $green = hexdec(substr($hexColor, 2, 2));
        $blue = hexdec(substr($hexColor, 4, 2));

        $image = imagecreatetruecolor(100, 100);

        return imagecolorallocate($image, $red, $green, $blue);
    }

    public function centerText()
    {
        [$imageWidth, $imageHeight] = getimagesize($this->getDiskImagePath());

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

    public function fillEditorProperties()
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
