<?php

namespace TomShaw\Mediable\Concerns;

use Livewire\Wireable;

final class ModalAlert implements Wireable
{
    public function __construct(
        public bool $show = false,
        public string $type = '',
        public string $message = ''
    ) {
    }

    public function toLivewire()
    {
        return [
            'show' => $this->show,
            'type' => $this->type,
            'message' => $this->message,
        ];
    }

    public static function fromLivewire($value)
    {
        return new self(
            show: $value['show'],
            type: $value['type'],
            message: $value['message']
        );
    }
}
