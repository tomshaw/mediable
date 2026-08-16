<?php

namespace TomShaw\Mediable\Image\Transformations;

use Illuminate\Contracts\Image\Transformation;

class Gamma implements Transformation
{
    public function __construct(
        public float $gamma,
    ) {}
}
