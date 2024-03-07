<?php

namespace TomShaw\Mediable\Concerns;

use Carbon\Carbon;
use Livewire\Wireable;
use TomShaw\Mediable\Models\Attachment;

final class ModelState implements Wireable
{
    public function __construct(
        public ?int $id = null,
        public ?string $fileName = '',
        public ?string $fileOriginalName = '',
        public ?string $fileType = '',
        public ?int $fileSize = 0,
        public ?string $fileDir = '',
        public ?string $fileUrl = '',
        public ?string $title = '',
        public ?string $caption = '',
        public ?string $description = '',
        public ?int $sortorder = 0,
        public ?string $styles = '',
        public ?string $createdAt = null,
        public ?string $updatedAt = null
    ) {
    }

    public static function fromAttachment(Attachment $attachment): self
    {
        return new self(
            id: $attachment->id,
            fileName: $attachment->file_name,
            fileOriginalName: $attachment->file_original_name,
            fileType: $attachment->file_type,
            fileSize: $attachment->file_size,
            fileDir: $attachment->file_dir,
            fileUrl: $attachment->file_url,
            title: $attachment->title,
            caption: $attachment->caption,
            description: $attachment->description,
            sortorder: $attachment->sortorder,
            styles: $attachment->styles,
            createdAt: $attachment->created_at,
            updatedAt: $attachment->updated_at,
        );
    }

    public function toLivewire()
    {
        return [
            'id' => $this->id,
            'fileName' => $this->fileName,
            'fileOriginalName' => $this->fileOriginalName,
            'fileType' => $this->fileType,
            'fileSize' => $this->fileSize,
            'fileDir' => $this->fileDir,
            'fileUrl' => $this->fileUrl,
            'title' => $this->title,
            'caption' => $this->caption,
            'description' => $this->description,
            'sortorder' => $this->sortorder,
            'styles' => $this->styles,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
        ];
    }

    public static function fromLivewire($value)
    {
        return new self(
            id: $value['id'],
            fileName: $value['fileName'],
            fileOriginalName: $value['fileOriginalName'],
            fileType: $value['fileType'],
            fileSize: $value['fileSize'],
            fileDir: $value['fileDir'],
            fileUrl: $value['fileUrl'],
            title: $value['title'],
            caption: $value['caption'],
            description: $value['description'],
            sortorder: $value['sortorder'],
            styles: $value['styles'],
            createdAt: $value['createdAt'],
            updatedAt: $value['updatedAt'],
        );
    }

    public function formatDateTime(string $value): string
    {
        return Carbon::parse($value)->format('F j, Y, g:i a');
    }
}
