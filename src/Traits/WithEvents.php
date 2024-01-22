<?php

namespace TomShaw\Mediable\Traits;

use TomShaw\Mediable\Enums\BrowserEvents;

trait WithEvents
{
    public function dispatchAlert(string $type, string $text): void
    {
        $this->dispatch(BrowserEvents::ALERT->value, type: $type, text: $text);
    }

    /**
     * @param  array<int, string>  $options
     */
    public function dispatchEvent(BrowserEvents $type, array $options = []): void
    {
        $this->dispatch($type, ...$options);
    }
}
