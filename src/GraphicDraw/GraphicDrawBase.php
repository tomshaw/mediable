<?php

namespace TomShaw\Mediable\GraphicDraw;

use GdImage;

class GraphicDrawBase
{
    public $flipModes = [
        IMG_FLIP_HORIZONTAL => 'Horizontal',
        IMG_FLIP_VERTICAL => 'Vertical',
        IMG_FLIP_BOTH => 'Both',
    ];

    public function getFlipModes()
    {
        return $this->flipModes;
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

    public function getimagesize(string $filename, &$image_info): array|false
    {
        return getimagesize($filename, $image_info);
    }

    public function scale(GdImage $image, int $new_width, int $new_height = -1, int $mode = IMG_BILINEAR_FIXED): GdImage|false
    {
        return imagescale($image, $new_width, $new_height, $mode);
    }

    public function crop(GdImage $image, array $rect): GdImage
    {
        return imagecrop($image, $rect);
    }

    public function flip(GdImage $image, int $mode): bool
    {
        return imageflip($image, $mode);
    }

    public function rotate(GdImage $image, float $angle, int $bgd_color, bool $ignore_transparent = false): GdImage|false
    {
        return imagerotate($image, $angle, $bgd_color, $ignore_transparent);
    }

    public function filter(GdImage $image, int $filter, array $args): bool
    {
        return imagefilter($image, $filter, ...$args);
    }

    public function text(GdImage $image, int $size, int $angle, int $x, int $y, int $color, string $fontfile, string $text): array|false
    {
        return imagettftext($image, $size, $angle, $x, $y, $color, $fontfile, $text);
    }
}
