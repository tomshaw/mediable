<?php

namespace TomShaw\Mediable\Image\Transformations;

use Illuminate\Contracts\Image\Transformation;

class Colorize implements Transformation
{
    /**
     * @param  int<-100, 100>  $red
     * @param  int<-100, 100>  $green
     * @param  int<-100, 100>  $blue
     */
    public function __construct(
        public int $red,
        public int $green,
        public int $blue,
    ) {}
}
