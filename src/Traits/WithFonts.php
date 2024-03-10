<?php

namespace TomShaw\Mediable\Traits;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use RegexIterator;

trait WithFonts
{
    public function buildRecursiveFontList(): array
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(__DIR__.'/../../resources/fonts', FilesystemIterator::FOLLOW_SYMLINKS)
        );

        $regex = new RegexIterator($iterator, '/^.+\.ttf$/i', RecursiveRegexIterator::GET_MATCH);

        $fonts = [];
        foreach ($regex as $font) {
            $fonts[] = $font[0];
        }

        return $fonts;
    }

    public function buildFontList(): array
    {
        $fonts = glob(__DIR__.'/../../resources/fonts/*.ttf');

        $options = [];
        foreach ($fonts as $font) {
            $fontName = basename($font, '.ttf');
            $fontName = str_replace('-', ' ', $fontName);

            $options[$font] = $fontName;
        }

        return $options;
    }
}
