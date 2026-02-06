<?php

namespace TomShaw\Mediable\GraphicDraw;

use GdImage;

class GraphicDrawBase
{
    /** @var array<int, string> */
    public array $flipModes = [
        IMG_FLIP_HORIZONTAL => 'Horizontal',
        IMG_FLIP_VERTICAL => 'Vertical',
        IMG_FLIP_BOTH => 'Both',
    ];

    /** @var array<int, string> */
    public array $filterModes = [
        IMG_FILTER_NEGATE => 'Negate',
        IMG_FILTER_GRAYSCALE => 'Grayscale',
        IMG_FILTER_BRIGHTNESS => 'Brightness',
        IMG_FILTER_CONTRAST => 'Contrast',
        IMG_FILTER_COLORIZE => 'Colorize',
        IMG_FILTER_EDGEDETECT => 'Edge Detect',
        IMG_FILTER_EMBOSS => 'Emboss',
        IMG_FILTER_GAUSSIAN_BLUR => 'Gaussian Blur',
        IMG_FILTER_SELECTIVE_BLUR => 'Selective Blur',
        IMG_FILTER_MEAN_REMOVAL => 'Mean Removal',
        IMG_FILTER_SMOOTH => 'Smooth',
        IMG_FILTER_PIXELATE => 'Pixelate',
    ];

    /** @var array<int, string> */
    public array $scaleModes = [
        IMG_NEAREST_NEIGHBOUR => 'Nearest Neighbour',
        IMG_BILINEAR_FIXED => 'Bilinear Fixed',
        IMG_BICUBIC => 'Bicubic',
    ];

    /** @return array<int, string> */
    public function getFlipModes(): array
    {
        return $this->flipModes;
    }

    /** @return array<int, string> */
    public function getFilterModes(): array
    {
        return $this->filterModes;
    }

    /** @return array<int, string> */
    public function getScaleModes(): array
    {
        return $this->scaleModes;
    }

    public function create(string $filename): GdImage|false
    {
        return match (strtolower(pathinfo($filename, PATHINFO_EXTENSION))) {
            'jpeg', 'jpg' => $this->imagecreatefromjpeg($filename),
            'png' => $this->imagecreatefrompng($filename),
            'gif' => $this->imagecreatefromgif($filename),
            'webp' => $this->imagecreatefromwebp($filename),
            'avif' => $this->imagecreatefromavif($filename),
            default => false,
        };
    }

    public function save(string $filename, GdImage $image): bool
    {
        return match (strtolower(pathinfo($filename, PATHINFO_EXTENSION))) {
            'jpeg', 'jpg' => $this->imagejpeg($image, $filename),
            'png' => $this->imagepng($image, $filename),
            'gif' => $this->imagegif($image, $filename),
            'webp' => $this->imagewebp($image, $filename),
            'avif' => $this->imageavif($image, $filename),
            default => false,
        };
    }

    public function imagecreatefrompath(string $filename): GdImage|false
    {
        return $this->create($filename);
    }

    public function imagecreatefromstring(string $data): GdImage|false
    {
        return imagecreatefromstring($data);
    }

    public function imagecreatefromjpeg(string $filename): GdImage|false
    {
        return imagecreatefromjpeg($filename);
    }

    public function imagecreatefrompng(string $filename): GdImage|false
    {
        return imagecreatefrompng($filename);
    }

    public function imagecreatefromgif(string $filename): GdImage|false
    {
        return imagecreatefromgif($filename);
    }

    public function imagecreatefromwebp(string $filename): GdImage|false
    {
        return imagecreatefromwebp($filename);
    }

    public function imagecreatefromavif(string $filename): GdImage|false
    {
        return imagecreatefromavif($filename);
    }

    public function imagewebp(GdImage $image, $file = null, int $quality = -1): bool
    {
        return imagewebp($image, $file, $quality);
    }

    public function imageavif(GdImage $image, $file = null, int $quality = -1): bool
    {
        return imageavif($image, $file, $quality);
    }

    public function imagejpeg(GdImage $image, $file = null, int $quality = -1): bool
    {
        return imagejpeg($image, $file, $quality);
    }

    public function imagepng(GdImage $image, $file = null, int $quality = -1, int $filters = -1): bool
    {
        return imagepng($image, $file, $quality, $filters);
    }

    public function imagegif(GdImage $image, $file = null): bool
    {
        return imagegif($image, $file);
    }

    public function imagedestroy(GdImage $image): bool
    {
        return imagedestroy($image);
    }

    public function getimagesize(string $filename, array $image_info = []): array|false
    {
        return getimagesize($filename, $image_info);
    }

    public function imagesy(GdImage $image): int|false
    {
        return imagesy($image);
    }

    public function imagesx(GdImage $image): int|false
    {
        return imagesx($image);
    }

    public function getImageMimeType(int $imageType): ?string
    {
        return image_type_to_mime_type($imageType);
    }

    public function getImageExtension(int $imageType): ?string
    {
        return image_type_to_extension($imageType);
    }

    public function scale(GdImage $image, int $new_width, int $new_height = -1, int $mode = IMG_BILINEAR_FIXED): GdImage|false
    {
        return imagescale($image, $new_width, $new_height, $mode);
    }

    public function crop(GdImage $image, array $rect): GdImage|false
    {
        return imagecrop($image, $rect);
    }

    public function flip(GdImage $image, int $mode): bool
    {
        return imageflip($image, $mode);
    }

    public function rotate(GdImage $image, float $angle, int $bgd_color): GdImage|false
    {
        return imagerotate($image, $angle, $bgd_color);
    }

    public function filter(GdImage $image, int $filter, array $args): bool
    {
        return imagefilter($image, $filter, ...$args);
    }

    public function text(GdImage $image, float $size, float $angle, int $x, int $y, int $color, string $fontfile, string $text): array|false
    {
        return imagettftext($image, $size, $angle, $x, $y, $color, $fontfile, $text);
    }
}
