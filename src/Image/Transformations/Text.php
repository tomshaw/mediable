<?php

namespace TomShaw\Mediable\Image\Transformations;

use Illuminate\Contracts\Image\Transformation;

class Text implements Transformation
{
    public function __construct(
        public string $text,
        public string $font,
        public float $size,
        public string $color,
        public float $angle = 0.0,
    ) {}
}
