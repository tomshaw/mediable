<?php

namespace TomShaw\Mediable\Traits;

/**
 * Trait ServerLimits
 */
trait ServerLimits
{
    /**
     * Get the maximum upload size.
     *
     * This method returns the smallest of the following values:
     * - The maximum upload file size from the PHP configuration.
     * - The maximum POST size from the PHP configuration.
     * - The memory limit from the PHP configuration.
     *
     * @return int The maximum upload size in bytes.
     */
    public function getMaxUploadSize(): int
    {
        $maxUpload = $this->convertToBytes(ini_get('upload_max_filesize'));
        $maxPost = $this->convertToBytes(ini_get('post_max_size'));
        $memoryLimit = $this->convertToBytes(ini_get('memory_limit'));

        return min($maxUpload, $maxPost, $memoryLimit);
    }

    /**
     * Get the maximum number of files that can be uploaded simultaneously.
     *
     * This method retrieves the 'max_file_uploads' configuration from the php.ini file.
     * The 'max_file_uploads' configuration directive limits the number of files that can be
     * uploaded in one request.
     *
     * @return int The maximum number of files that can be uploaded simultaneously.
     */
    public function getMaxFileUploads(): int
    {
        return (int) ini_get('max_file_uploads');
    }

    /**
     * Get the maximum upload file size from the PHP configuration.
     *
     * This method retrieves the 'upload_max_filesize' configuration from the php.ini file,
     * converts it to an integer, and returns it.
     *
     * @return int The maximum upload file size in bytes.
     */
    public function getMaxUploadFileSize(): int
    {
        return $this->convertToBytes(ini_get('upload_max_filesize'));
    }

    /**
     * Get the maximum size of POST data that PHP will accept.
     *
     * This method retrieves the 'post_max_size' configuration from the php.ini file,
     * converts it to an integer, and returns it.
     *
     * @return int The maximum size of POST data that PHP will accept, in bytes.
     */
    public function getPostMaxSize(): int
    {
        return $this->convertToBytes(ini_get('post_max_size'));
    }

    /**
     * Get the maximum amount of memory that a script is allowed to consume.
     *
     * This method retrieves the 'memory_limit' configuration from the php.ini file,
     * converts it to an integer, and returns it.
     *
     * @return int The maximum amount of memory a script is allowed to consume, in bytes.
     */
    public function getMemoryLimit(): int
    {
        return $this->convertToBytes(ini_get('memory_limit'));
    }

    /**
     * Convert a shorthand byte value from a PHP configuration directive to an integer.
     *
     * @param  string  $value  The shorthand byte value to convert (e.g. '128M', '2G', '1K').
     * @return int The byte value as an integer.
     */
    private function convertToBytes(string $value): int
    {
        $value = trim($value);
        $suffix = strtolower($value[strlen($value) - 1]);
        $numeric = (int) $value;

        return match ($suffix) {
            'g' => $numeric * 1_073_741_824,
            'm' => $numeric * 1_048_576,
            'k' => $numeric * 1_024,
            default => $numeric,
        };
    }
}
