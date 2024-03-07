<?php

namespace TomShaw\Mediable\Traits;

trait WithMimeTypes
{
    protected $strategies = [
        'image' => [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
            'image/avif',
            'image/bmp',
            'image/tiff',
        ],
        'video' => [
            'video/3gpp',
            'video/mpeg',
            'video/mp4',
            'video/ogg',
            'video/quicktime',
            'video/webm',
            'video/x-flv',
            'video/x-msvideo',
        ],
        'audio' => [
            'audio/aac',
            'audio/flac',
            'audio/midi',
            'audio/mpeg',
            'audio/ogg',
            'audio/ogg',
            'audio/opus',
            'audio/wav',
            'audio/webm',
            'audio/x-midi',
            'audio/x-wav',
        ],
        'document' => [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'text/plain',
            'application/rtf',
            'application/vnd.oasis.opendocument.text',
            'application/vnd.oasis.opendocument.spreadsheet',
            'application/vnd.oasis.opendocument.presentation',
            'application/vnd.visio',
            'application/vnd.ms-outlook',
            'application/xml',
            'text/csv',
        ],
        'archive' => [
            'application/zip',
            'application/x-rar-compressed',
            'application/x-7z-compressed',
            'application/x-tar',
            'application/gzip',
            'application/x-bzip2',
            'application/x-zip-compressed',
            'application/x-lzma',
            'application/x-lzx',
            'application/x-gtar',
            'application/x-gzip',
            'application/x-lzh',
            'application/x-lha',
            'application/x-tar',
            'application/x-compress',
            'application/x-compressed',
            'application/x-stuffit',
            'application/x-stuffitx',
            'application/x-gtar',
            'application/x-gzip',
            'application/vnd.android.package-archive',
        ],
    ];

    public function mimeTypeImage(string $mimeType): bool
    {
        return in_array($mimeType, $this->strategies['image']);
    }

    public function mimeTypeAudio(string $mimeType): bool
    {
        return in_array($mimeType, $this->strategies['audio']);
    }

    public function mimeTypeVideo(string $mimeType): bool
    {
        return in_array($mimeType, $this->strategies['video']);
    }

    public function mimeTypeDocument(string $mimeType): bool
    {
        return in_array($mimeType, $this->strategies['document']);
    }

    public function mimeTypeArchive(string $mimeType): bool
    {
        return in_array($mimeType, $this->strategies['archive']);
    }

    public function formatMimeType(string $mimeType): string
    {
        $parts = explode('/', $mimeType);

        return strtoupper(end($parts));
    }
}
