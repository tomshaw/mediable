<?php

namespace TomShaw\Mediable\Traits;

trait WithMimeTypes
{
    /**
     * Determine if the given mime type is an image.
     *
     * @param  string  $mimeType
     * @return bool
     */
    public function mimeTypeImage($mimeType): bool
    {
        return in_array($mimeType, config('mediable.mimes.image'));
    }

    /**
     * Determine if the given mime type is a video.
     *
     * @param  string  $mimeType
     * @return bool
     */
    public function mimeTypeAudio($mimeType): bool
    {
        return in_array($mimeType, config('mediable.mimes.audio'));
    }

    /**
     * Determine if the given mime type is a video.
     *
     * @param  string  $mimeType
     * @return bool
     */
    public function mimeTypeVideo($mimeType): bool
    {
        return in_array($mimeType, config('mediable.mimes.video'));
    }

    /**
     * Determine if the given mime type is a document.
     *
     * @param  string  $mimeType
     * @return bool
     */
    public function mimeTypeDocument($mimeType): bool
    {
        return in_array($mimeType, config('mediable.mimes.document'));
    }

    /**
     * Determine if the given mime type is a archive.
     *
     * @param  string  $mimeType
     * @return bool
     */
    public function mimeTypeArchive($mimeType): bool
    {
        return in_array($mimeType, config('mediable.mimes.archive'));
    }

    /**
     * Determine if the given mime type is a file.
     *
     * @param  string  $mimeType
     */
    public function mimeType($mimeType): string
    {
        if ($this->mimeTypeImage($mimeType)) {
            return 'image';
        }

        if ($this->mimeTypeAudio($mimeType)) {
            return 'audio';
        }

        if ($this->mimeTypeVideo($mimeType)) {
            return 'video';
        }

        if ($this->mimeTypeDocument($mimeType)) {
            return 'document';
        }

        return 'file';
    }
}
