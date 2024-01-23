<?php

namespace TomShaw\Mediable\Traits;

trait WithConfig
{
    public function getMaxUploadSize()
    {
        $maxUpload = (int) (ini_get('upload_max_filesize'));
        $maxPost = (int) (ini_get('post_max_size'));
        $memoryLimit = (int) (ini_get('memory_limit'));

        return min($maxUpload, $maxPost, $memoryLimit);
    }

    public function getMaxFileUploads()
    {
        return ini_get('max_file_uploads');
    }
}
