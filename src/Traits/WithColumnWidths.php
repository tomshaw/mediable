<?php

namespace TomShaw\Mediable\Traits;

trait WithColumnWidths
{
    public array $columnWidths = [100, 50, 33.3, 25, 20, 16.66, 14.28, 12.5, 11.11, 10, 9.09, 8.33];

    public int $defaultColumnWidth = 4;

    public function normalizeColumnPadding(float $columnWidth): float
    {
        $padding = 2;

        switch ($columnWidth) {
            case 100:
                $padding = 6;
                break;
            case 50:
                $padding = 5;
                break;
            case 33.3:
                $padding = 4.5;
                break;
            case 25:
                $padding = 4;
                break;
            case 20:
                $padding = 3.5;
                break;
            case 16.66:
                $padding = 3;
                break;
            case 14.28:
                $padding = 3;
                break;
            case 12.5:
                $padding = 3;
                break;
            case 11.11:
                $padding = 2.5;
                break;
            case 10:
                $padding = 2;
                break;
            case 9.09:
                $padding = 2;
                break;
            case 8.33:
                $padding = 2;
                break;
            default:
                $padding = 2;
                break;
        }

        return $padding;
    }
}
