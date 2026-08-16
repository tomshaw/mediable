<?php

namespace TomShaw\Mediable\Image\Transformations;

use Illuminate\Contracts\Image\Transformation;

class Pixelate implements Transformation
{
    /**
     * @param  int<1, max>  $size
     */
    public function __construct(
        public int $size,
    ) {}
}
