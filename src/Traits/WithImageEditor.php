<?php

namespace TomShaw\Mediable\Traits;

use TomShaw\Mediable\Eloquent\Eloquent;
use TomShaw\Mediable\Enums\BrowserEvents;
use TomShaw\Mediable\Image\ImageEditor;

trait WithImageEditor
{
    public ?string $flipMode = null;

    public string $fitMode = 'scale';

    public int $fitWidth = 0;

    public int $fitHeight = 0;

    public string $fitBackground = '#ffffff';

    public ?string $filterMode = null;

    public int $brightness = 0;

    public int $contrast = 0;

    public int $colorizeRed = 0;

    public int $colorizeGreen = 0;

    public int $colorizeBlue = 0;

    public float $gamma = 1.0;

    public int $blurAmount = 5;

    public int $sharpenAmount = 10;

    public int $pixelateSize = 10;

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

    public string $convertFormat = 'webp';

    public int $convertQuality = 90;

    /** @var list<array<string, mixed>> */
    public array $editHistory = [];

    public ?string $selectedForm = '';

    /** @var array<string, string> */
    public array $availableForms = [
        'image-fit' => 'Resize & Fit',
        'image-crop' => 'Crop Image',
        'image-flip' => 'Flip Image',
        'image-rotate' => 'Rotate Image',
        'image-orient' => 'Auto Orient',
        'image-filter' => 'Filters',
        'image-text' => 'Add Text',
        'image-convert' => 'Convert Format',
    ];

    public function setForm(string $key): void
    {
        $this->selectedForm = $key;
    }

    public function resetForm(): void
    {
        $this->selectedForm = '';
    }

    /** @return array<string, string> */
    public function getFlipModes(): array
    {
        return ImageEditor::flipModes();
    }

    /** @return array<string, string> */
    public function getFitModes(): array
    {
        return ImageEditor::fitModes();
    }

    /** @return array<string, string> */
    public function getFilterModes(): array
    {
        return ImageEditor::filterModes();
    }

    /** @return array<string, string> */
    public function getFormats(): array
    {
        return ImageEditor::formats();
    }

    /**
     * The disk relative path of the working copy currently open in the editor.
     */
    public function getStoragePath(): ?string
    {
        return $this->attachment?->file_dir ?: null;
    }

    /**
     * @return array{width: int, height: int}|null
     */
    public function getImageDimensions(): ?array
    {
        $path = $this->getStoragePath();

        return $path ? ImageEditor::dimensions($path) : null;
    }

    public function updatedFitWidth(): void
    {
        if ($this->fitMode !== 'scale') {
            return;
        }

        $dimensions = $this->getImageDimensions();

        if (! $dimensions || $dimensions['height'] === 0) {
            return;
        }

        $this->fitHeight = (int) round($this->fitWidth / ($dimensions['width'] / $dimensions['height']));
    }

    public function updatedFitHeight(): void
    {
        if ($this->fitMode !== 'scale') {
            return;
        }

        $dimensions = $this->getImageDimensions();

        if (! $dimensions || $dimensions['height'] === 0) {
            return;
        }

        $this->fitWidth = (int) round($this->fitHeight * ($dimensions['width'] / $dimensions['height']));
    }

    public function flipImage(): void
    {
        $path = $this->getStoragePath();

        if (! $path || ! $this->flipMode) {
            return;
        }

        $this->finishEdit(ImageEditor::flip($path, $this->flipMode));
    }

    public function fitImage(): void
    {
        $path = $this->getStoragePath();

        if (! $path) {
            return;
        }

        $this->finishEdit(ImageEditor::fit(
            $path,
            $this->fitMode,
            $this->fitWidth,
            $this->fitHeight,
            $this->fitMode === 'contain' ? $this->fitBackground : null,
        ));
    }

    public function filterImage(): void
    {
        $path = $this->getStoragePath();

        if (! $path || ! $this->filterMode) {
            return;
        }

        $this->finishEdit(ImageEditor::filter($path, $this->filterMode, $this->getFilterOptions()));
    }

    public function rotateImage(): void
    {
        $path = $this->getStoragePath();

        if (! $path || ! $this->rotateAngle) {
            return;
        }

        $this->finishEdit(ImageEditor::rotate($path, (float) $this->rotateAngle, $this->rotateBgColor));
    }

    public function cropImage(): void
    {
        $path = $this->getStoragePath();

        if (! $path) {
            return;
        }

        $this->finishEdit(ImageEditor::crop($path, $this->cropWidth, $this->cropHeight, $this->cropX, $this->cropY));
    }

    public function orientImage(): void
    {
        $path = $this->getStoragePath();

        if (! $path) {
            return;
        }

        $this->finishEdit(ImageEditor::orient($path));
    }

    public function addText(): void
    {
        $path = $this->getStoragePath();

        if (! $path) {
            return;
        }

        $this->finishEdit(ImageEditor::text(
            $path,
            $this->imageText,
            $this->imageFont,
            $this->imageFontSize,
            $this->imageTextColor,
            (float) $this->imageTextAngle,
        ));
    }

    /**
     * Re-encode the working copy and repoint its attachment record at the new file.
     */
    public function convertImage(): void
    {
        $path = $this->getStoragePath();

        if (! $path || ! $this->attachment?->id) {
            return;
        }

        $destination = ImageEditor::convert($path, $this->convertFormat, $this->convertQuality);

        if ($destination === false) {
            $this->finishEdit(false);

            return;
        }

        Eloquent::replaceAttachmentFile($this->attachment->id, $path, $destination);

        $this->finishEdit(true);
    }

    /**
     * @return array<string, int|float>
     */
    protected function getFilterOptions(): array
    {
        return match ($this->filterMode) {
            'brightness' => ['level' => $this->brightness],
            'contrast' => ['level' => $this->contrast],
            'colorize' => ['red' => $this->colorizeRed, 'green' => $this->colorizeGreen, 'blue' => $this->colorizeBlue],
            'gamma' => ['gamma' => $this->gamma],
            'blur' => ['amount' => $this->blurAmount],
            'sharpen' => ['amount' => $this->sharpenAmount],
            'pixelate' => ['size' => $this->pixelateSize],
            default => [],
        };
    }

    protected function finishEdit(bool $saved): void
    {
        if (! $saved) {
            $this->dispatch(BrowserEvents::ALERT->value, [
                'type' => 'error',
                'message' => 'Failed to save image changes.',
            ]);

            return;
        }

        $this->refreshWorkingCopy();

        $this->editHistory[] = $this->getEditorSettings();
    }

    /**
     * @return array<string, mixed>
     */
    public function getEditorSettings(): array
    {
        return [
            'flipMode' => $this->flipMode,
            'fitMode' => $this->fitMode,
            'fitWidth' => $this->fitWidth,
            'fitHeight' => $this->fitHeight,
            'fitBackground' => $this->fitBackground,
            'filterMode' => $this->filterMode,
            'brightness' => $this->brightness,
            'contrast' => $this->contrast,
            'colorizeRed' => $this->colorizeRed,
            'colorizeGreen' => $this->colorizeGreen,
            'colorizeBlue' => $this->colorizeBlue,
            'gamma' => $this->gamma,
            'blurAmount' => $this->blurAmount,
            'sharpenAmount' => $this->sharpenAmount,
            'pixelateSize' => $this->pixelateSize,
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
            'convertFormat' => $this->convertFormat,
            'convertQuality' => $this->convertQuality,
            'primaryId' => $this->primaryId,
        ];
    }

    public function fillEditorProperties(): void
    {
        $this->fill([
            'flipMode' => null,
            'fitMode' => 'scale',
            'fitWidth' => 0,
            'fitHeight' => 0,
            'fitBackground' => '#ffffff',
            'filterMode' => null,
            'brightness' => 0,
            'contrast' => 0,
            'colorizeRed' => 0,
            'colorizeGreen' => 0,
            'colorizeBlue' => 0,
            'gamma' => 1.0,
            'blurAmount' => 5,
            'sharpenAmount' => 10,
            'pixelateSize' => 10,
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
            'convertFormat' => 'webp',
            'convertQuality' => 90,
        ]);
    }
}
