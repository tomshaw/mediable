<?php

namespace TomShaw\Mediable\Traits;

trait WithFileSize
{
    public function formatBytes(int|float|null $bytes, int $precision = 2): string
    {
        if ($bytes === null) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, $precision).' '.$units[$pow];
    }
}
