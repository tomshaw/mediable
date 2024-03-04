<?php

namespace TomShaw\Mediable\Concerns;

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
        public ?string $created_at = null,
        public ?string $updated_at = null
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
            created_at: $attachment->created_at,
            updated_at: $attachment->updated_at,
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
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
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
            created_at: $value['created_at'],
            updated_at: $value['updated_at'],
        );
    }
}
