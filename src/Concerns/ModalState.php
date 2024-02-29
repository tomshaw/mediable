<?php

namespace TomShaw\Mediable\Concerns;

use Livewire\Wireable;

final class ModalState implements Wireable
{
    public function __construct(
        public bool $show = false,
        public string $elementId = ''
    ) {
    }

    public function toLivewire()
    {
        return [
            'show' => $this->show,
            'elementId' => $this->elementId,
        ];
    }

    public static function fromLivewire($value)
    {
        return new self(
            show: $value['show'],
            elementId: $value['elementId'],
        );
    }

    public function hasElementId(): bool
    {
        return isset($this->elementId) && ! empty($this->elementId);
    }
}
