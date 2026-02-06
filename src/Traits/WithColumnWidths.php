<?php

namespace TomShaw\Mediable\Traits;

trait WithColumnWidths
{
    public array $columnWidths = [100, 50, 33.3, 25, 20, 16.66, 14.28, 12.5, 11.11, 10, 9.09, 8.33];

    public int $defaultColumnWidth = 4;

    public function normalizeColumnPadding(float $columnWidth): float
    {
        return match ($columnWidth) {
            100.0 => 6.0,
            50.0 => 5.0,
            33.3 => 4.5,
            25.0 => 4.0,
            20.0 => 3.5,
            16.66, 14.28, 12.5 => 3.0,
            11.11 => 2.5,
            default => 2.0,
        };
    }
}
