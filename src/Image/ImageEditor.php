<?php

namespace TomShaw\Mediable\Image;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array<string, string> flipModes()
 * @method static array<string, string> fitModes()
 * @method static array<string, string> filterModes()
 * @method static array<string, string> formats()
 * @method static bool exists(string $path)
 * @method static array{width: int, height: int}|null dimensions(string $path)
 * @method static bool flip(string $path, string $mode)
 * @method static bool fit(string $path, string $mode, int $width, int $height, ?string $background = null)
 * @method static bool filter(string $path, string $mode, array<string, int|float> $options = [])
 * @method static bool rotate(string $path, float $angle, ?string $background = null)
 * @method static bool crop(string $path, int $width, int $height, int $x = 0, int $y = 0)
 * @method static bool orient(string $path)
 * @method static bool text(string $path, string $text, string $font, float $size, string $color, float $angle = 0.0)
 * @method static string|false convert(string $path, string $format, ?int $quality = null)
 *
 * @see ImageEditorManager
 */
class ImageEditor extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return ImageEditorManager::class;
    }
}
