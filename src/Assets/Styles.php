<?php

namespace TomShaw\Mediable\Assets;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Styles extends Component
{
    public function render(): View
    {
        /** @phpstan-ignore argument.type */
        return view('mediable::assets.styles');
    }
}
