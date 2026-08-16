<?php

use Illuminate\Support\Facades\Image;
use TomShaw\Mediable\Tests\Support\TestCase;

uses(TestCase::class)->in(__DIR__);

/**
 * Build a two tone PNG so geometry changes stay observable after a transformation.
 */
function fixtureImage(int $width = 120, int $height = 60): string
{
    $image = imagecreatetruecolor($width, $height);

    imagefilledrectangle($image, 0, 0, (int) ($width / 2) - 1, $height, imagecolorallocate($image, 200, 40, 40));
    imagefilledrectangle($image, (int) ($width / 2), 0, $width, $height, imagecolorallocate($image, 40, 40, 200));

    ob_start();
    imagepng($image);
    $bytes = (string) ob_get_clean();

    imagedestroy($image);

    return $bytes;
}

/**
 * Build a single color PNG so color changes can be asserted exactly.
 */
function solidImage(int $width, int $height, int $red, int $green, int $blue): string
{
    $image = imagecreatetruecolor($width, $height);

    imagefilledrectangle($image, 0, 0, $width, $height, imagecolorallocate($image, $red, $green, $blue));

    ob_start();
    imagepng($image);
    $bytes = (string) ob_get_clean();

    imagedestroy($image);

    return $bytes;
}

function dominantColor(string $path): string
{
    return Image::fromStorage($path, 'public')->dominantColor();
}

function font(): string
{
    return __DIR__.'/../resources/fonts/Inter-Bold.ttf';
}
