<?php

namespace TomShaw\Mediable\Image\Transformations;

use Illuminate\Contracts\Image\Transformation;

class Brightness implements Transformation
{
    /**
     * @param  int<-100, 100>  $level
     */
    public function __construct(
        public int $level,
    ) {}
}
