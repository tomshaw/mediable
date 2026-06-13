<?php

namespace TomShaw\Mediable\Concerns;

use Livewire\Wireable;

final class ModalState implements Wireable
{
    public function __construct(
        public bool $show = false,
        public string $elementId = ''
    ) {}

    /**
     * @return array{show: bool, elementId: string}
     */
    public function toLivewire(): array
    {
        return [
            'show' => $this->show,
            'elementId' => $this->elementId,
        ];
    }

    /**
     * @param  array{show: bool, elementId: string}  $value
     */
    public static function fromLivewire($value): self
    {
        return new self(
            show: $value['show'],
            elementId: $value['elementId'],
        );
    }

    public function hasElementId(): bool
    {
        return ! empty($this->elementId);
    }
}
