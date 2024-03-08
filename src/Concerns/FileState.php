<?php

namespace TomShaw\Mediable\Concerns;

use Livewire\Wireable;

final class FileState implements Wireable
{
    public function __construct(
        public ?string $file_type = '',
        public ?string $file_dir = '',
        public ?string $file_url = '',
    ) {
    }

    public function toLivewire()
    {
        return [
            'file_type' => $this->file_type,
            'file_dir' => $this->file_dir,
            'file_url' => $this->file_url,
        ];
    }

    public static function fromLivewire($value)
    {
        return new self(
            file_type: $value['file_type'],
            file_dir: $value['file_dir'],
            file_url: $value['file_url'],
        );
    }
}
