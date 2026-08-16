<?php

namespace TomShaw\Mediable\Image;

use Intervention\Image\Alignment;
use Intervention\Image\Interfaces\ImageInterface;
use Intervention\Image\Typography\FontFactory;
use TomShaw\Mediable\Image\Transformations\{Brightness, Colorize, Contrast, Gamma, Invert, Pixelate, Text};

/**
 * Handlers for the editor transformations Laravel's image API does not ship with.
 *
 * Both bundled drivers process images through Intervention, so a single handler serves each.
 */
class ImageTransformations
{
    /** @var list<string> */
    public const DRIVERS = ['gd', 'imagick'];

    /**
     * @return array<class-string, callable>
     */
    public static function handlers(): array
    {
        return [
            Invert::class => fn (ImageInterface $image): ImageInterface => $image->invert(),

            Brightness::class => fn (ImageInterface $image, Brightness $transformation): ImageInterface => $image->brightness($transformation->level),

            Contrast::class => fn (ImageInterface $image, Contrast $transformation): ImageInterface => $image->contrast($transformation->level),

            Colorize::class => fn (ImageInterface $image, Colorize $transformation): ImageInterface => $image->colorize(
                $transformation->red,
                $transformation->green,
                $transformation->blue,
            ),

            Gamma::class => fn (ImageInterface $image, Gamma $transformation): ImageInterface => $image->gamma($transformation->gamma),

            Pixelate::class => fn (ImageInterface $image, Pixelate $transformation): ImageInterface => $image->pixelate($transformation->size),

            Text::class => fn (ImageInterface $image, Text $transformation): ImageInterface => $image->text(
                $transformation->text,
                (int) round($image->width() / 2),
                (int) round($image->height() / 2),
                fn (FontFactory $font) => $font
                    ->filename($transformation->font)
                    ->size($transformation->size)
                    ->color($transformation->color)
                    ->angle($transformation->angle)
                    ->align(Alignment::CENTER, Alignment::CENTER),
            ),
        ];
    }
}
