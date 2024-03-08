<?php

namespace TomShaw\Mediable\Concerns;

use Carbon\Carbon;
use Livewire\Wireable;
use TomShaw\Mediable\Models\Attachment;

final class AttachmentState implements Wireable
{
    public function __construct(
        public ?int $id = null,
        public ?string $file_name = '',
        public ?string $file_original_name = '',
        public ?string $file_type = '',
        public ?int $file_size = 0,
        public ?string $file_dir = '',
        public ?string $file_url = '',
        public ?string $title = '',
        public ?string $caption = '',
        public ?string $description = '',
        public ?int $sortorder = 0,
        public ?string $styles = '',
        public ?string $created_at = null,
        public ?string $updated_at = null
    ) {
    }

    public static function fromAttachment(Attachment $attachment): self
    {
        return new self(
            id: $attachment->id,
            file_name: $attachment->file_name,
            file_original_name: $attachment->file_original_name,
            file_type: $attachment->file_type,
            file_size: $attachment->file_size,
            file_dir: $attachment->file_dir,
            file_url: $attachment->file_url,
            title: $attachment->title,
            caption: $attachment->caption,
            description: $attachment->description,
            sortorder: $attachment->sortorder,
            styles: $attachment->styles,
            created_at: $attachment->created_at,
            updated_at: $attachment->updated_at,
        );
    }

    public function toLivewire()
    {
        return [
            'id' => $this->id,
            'file_name' => $this->file_name,
            'file_original_name' => $this->file_original_name,
            'file_type' => $this->file_type,
            'file_size' => $this->file_size,
            'file_dir' => $this->file_dir,
            'file_url' => $this->file_url,
            'title' => $this->title,
            'caption' => $this->caption,
            'description' => $this->description,
            'sortorder' => $this->sortorder,
            'styles' => $this->styles,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    public static function fromLivewire($value)
    {
        return new self(
            id: $value['id'],
            file_name: $value['file_name'],
            file_original_name: $value['file_original_name'],
            file_type: $value['file_type'],
            file_size: $value['file_size'],
            file_dir: $value['file_dir'],
            file_url: $value['file_url'],
            title: $value['title'],
            caption: $value['caption'],
            description: $value['description'],
            sortorder: $value['sortorder'],
            styles: $value['styles'],
            created_at: $value['created_at'],
            updated_at: $value['updated_at'],
        );
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getFileName(): string
    {
        return $this->file_name;
    }

    public function getOriginalFileName(): string
    {
        return $this->file_original_name;
    }

    public function getFileType(): string
    {
        return $this->file_type;
    }

    public function getFileSize(): int
    {
        return $this->file_size;
    }

    public function getFileDir(): string
    {
        return $this->file_dir;
    }

    public function getFileUrl(): string
    {
        return $this->file_url;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getCaption(): string
    {
        return $this->caption;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getSortOrder(): int
    {
        return $this->sortorder;
    }

    public function getStyles(): string
    {
        return $this->styles;
    }

    public function getCreatedAt(string $format = 'F j, Y, g:i a'): string
    {
        return Carbon::parse($this->created_at)->format($format);
    }

    public function getUpdatedAt(string $format = 'F j, Y, g:i a'): string
    {
        return Carbon::parse($this->updated_at)->format($format);
    }

    public function formatDateTime(string $value): string
    {
        return Carbon::parse($value)->format('F j, Y, g:i a');
    }
}
