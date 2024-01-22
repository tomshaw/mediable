<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Storage
    |--------------------------------------------------------------------------
    |
    | The disk name to store file, the value is key of `disks` in `config/filesystems.php`
    | config('filesystems.disks')
    */
    'disk' => env('FILESYSTEM_DRIVER', 'public'),

    /*
    |--------------------------------------------------------------------------
    | Theme
    |--------------------------------------------------------------------------
    |
    |
    |
    */
    'theme' => 'tailwind',

    /*
    |--------------------------------------------------------------------------
    | Validation Allowable File Types/Max File Size
    |--------------------------------------------------------------------------
    |
    | 5120   = 5MB Max
    | 10240  = 10MB Max
    | 51200  = 50MB Max
    | 102400 = 100MB Max
    | 204800 = 200MB Max
    |
    */
    'validation' => [
        'files.*' => 'required|mimes:jpeg,png,jpg,gif,mp3,mp4,m4a,ogg,wav,webm,avi,mov,wmv,txt,pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar|max:10240',
    ],

    /*
    |--------------------------------------------------------------------------
    | Mime Types
    |--------------------------------------------------------------------------
    |
    | 100MB Max
    |
    */
    'mimes' => [
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
    ],
];
