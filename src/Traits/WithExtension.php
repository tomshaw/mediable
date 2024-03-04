<?php

namespace TomShaw\Mediable\Traits;

use TomShaw\Mediable\Exceptions\MediaExtensionException;

trait WithExtension
{
    public function hasExtension(string $extension): void
    {
        if (! extension_loaded($extension)) {
            throw new MediaExtensionException("The extension '{$extension}' is not loaded.");
        }
    }
}
