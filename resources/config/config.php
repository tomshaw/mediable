<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Theme
    |--------------------------------------------------------------------------
    |
    | This configuration option allows you to change the default theme for the
    | application. By default, we use the Tailwind theme.
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
    | Storage Disk
    |--------------------------------------------------------------------------
    |
    | The disk defined in the filesystems.php config file to use for storing
    | uploaded files.
    */
    'disk' => env('MEDIABLE_DISK_DRIVER', 'public'),

    /*
    |--------------------------------------------------------------------------
    | Folder
    |--------------------------------------------------------------------------
    |
    | The folder on the system disk to store uploaded files.
    */
    'folder' => env('MEDIABLE_DISK_FOLDER', 'uploads'),

    /*
    |--------------------------------------------------------------------------
    | Image Conversion Settings
    |--------------------------------------------------------------------------
    |
    | These settings control the creation of WebP and AVIF versions of image uploads.
    |
    | 'create_webp' and 'create_avif' determine whether to create WebP and AVIF versions, respectively.
    | These can be set to true or false. By default, both are set to true.
    |
    | 'webp_quality' and 'avif_quality' control the quality of the WebP and AVIF versions, respectively.
    | These can be set to any integer between 1 and 100. By default, both are set to 80.
    |
    | Conversions run through Laravel's image manipulation API. The encoder is selected by the
    | 'images.default' config option (the IMAGE_DRIVER environment variable), which supports
    | 'gd' and 'imagick'. AVIF requires an encoder built with AVIF support.
    |
    */
    'create_webp' => env('MEDIABLE_CREATE_WEBP', true),
    'create_avif' => env('MEDIABLE_CREATE_AVIF', true),
    'webp_quality' => (int) env('MEDIABLE_WEBP_QUALITY', 80),
    'avif_quality' => (int) env('MEDIABLE_AVIF_QUALITY', 80),

    /*
    |--------------------------------------------------------------------------
    | Image Editor Quality
    |--------------------------------------------------------------------------
    |
    | Every image editor operation re-encodes the file it writes back to disk. This
    | setting controls the quality of that encode, and can be set to any integer
    | between 1 and 100. Formats without a quality setting ignore it.
    |
    */
    'editor_quality' => (int) env('MEDIABLE_EDITOR_QUALITY', 90),
];
