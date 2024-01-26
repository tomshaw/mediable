<?php

return [
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
    | The disk name to store file, the value is key of `disks` in `config/filesystems.php`
    | config('filesystems.disks')
    */
    'disk' => env('FILESYSTEM_DRIVER', 'public'),

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
];
