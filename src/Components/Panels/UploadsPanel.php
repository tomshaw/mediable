<?php

namespace TomShaw\Mediable\Components\Panels;

use Livewire\Attributes\{On, Reactive};
use Livewire\{Component, WithFileUploads};

class UploadsPanel extends Component
{
    use WithFileUploads;

    public array $files = [];

    #[Reactive]
    public ?int $maxUploadSize = null;

    #[Reactive]
    public ?int $maxFileUploads = null;

    #[Reactive]
    public ?int $maxUploadFileSize = null;

    #[Reactive]
    public ?int $postMaxSize = null;

    #[Reactive]
    public ?int $memoryLimit = null;

    public function updatedFiles(): void
    {
        $this->validate(config('mediable.validation'));
    }

    public function clearFile(int $index): void
    {
        array_splice($this->files, $index, 1);
    }

    public function clearFiles(): void
    {
        $this->files = [];
    }

    public function createAttachments(): void
    {
        $this->dispatch('panel:create-attachments', files: $this->files);
        $this->files = [];
    }

    public function getTotalUploadSize(): int
    {
        return array_reduce($this->files, function ($carry, $file) {
            return $carry + $file->getSize();
        }, 0);
    }

    public function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision).' '.$units[$pow];
    }

    #[On('panel:alert')]
    public function handleAlert(string $type, string $message): void
    {
        $this->dispatch('media.alert', event: ['type' => $type, 'message' => $message]);
    }

    public function render()
    {
        return view('mediable::livewire.components.uploads-panel');
    }
}
