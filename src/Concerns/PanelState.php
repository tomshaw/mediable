<?php

namespace TomShaw\Mediable\Concerns;

use Livewire\Wireable;

final class PanelState implements Wireable
{
    public function __construct(
        public bool $thumbMode = false,
        public bool $previewMode = false,
        public bool $uploadMode = false,
        public bool $editorMode = false,
        public bool $formMode = false
    ) {
    }

    public function isThumbMode(): bool
    {
        return $this->thumbMode;
    }

    public function isPreviewMode(): bool
    {
        return $this->previewMode;
    }

    public function isUploadMode(): bool
    {
        return $this->uploadMode;
    }

    public function isEditorMode(): bool
    {
        return $this->editorMode;
    }

    public function isFormMode(): bool
    {
        return $this->formMode;
    }

    public function toLivewire()
    {
        return [
            'thumbMode' => $this->thumbMode,
            'previewMode' => $this->previewMode,
            'uploadMode' => $this->uploadMode,
            'editorMode' => $this->editorMode,
            'formMode' => $this->formMode,
        ];
    }

    public static function fromLivewire($value)
    {
        return new self(
            thumbMode: $value['thumbMode'],
            previewMode: $value['previewMode'],
            uploadMode: $value['uploadMode'],
            editorMode: $value['editorMode'],
            formMode: $value['formMode']
        );
    }
}
