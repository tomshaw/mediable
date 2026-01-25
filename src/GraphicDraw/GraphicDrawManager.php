<?php

namespace TomShaw\Mediable\GraphicDraw;

class GraphicDrawManager extends GraphicDrawBase
{
    public function flipAndSave(string $filename, int $mode): bool
    {
        $image = $this->create($filename);

        if ($image === false) {
            return false;
        }

        $this->flip($image, $mode);

        return $this->save($filename, $image);
    }

    public function filterAndSave(string $filename, int $filter, array $args = []): bool
    {
        $image = $this->create($filename);

        if ($image === false) {
            return false;
        }

        $this->filter($image, $filter, $args);

        return $this->save($filename, $image);
    }

    public function scaleAndSave(string $filename, int $new_width, int $new_height = -1, int $mode = IMG_BILINEAR_FIXED): bool
    {
        $image = $this->create($filename);

        if ($image === false) {
            return false;
        }

        $result = $this->scale($image, $new_width, $new_height, $mode);

        return $this->save($filename, $result);
    }

    public function rotateAndSave(string $filename, float $angle, int $bgd_color): bool
    {
        $image = $this->create($filename);

        if ($image === false) {
            return false;
        }

        $result = $this->rotate($image, $angle, $bgd_color);

        return $this->save($filename, $result);
    }

    public function cropAndSave(string $filename, array $rect): bool
    {
        $image = $this->create($filename);

        if ($image === false) {
            return false;
        }

        $result = $this->crop($image, $rect);

        return $this->save($filename, $result);
    }

    public function textAndSave(string $filename, float $size, float $angle, int $x, int $y, int $color, string $fontfile, string $text): bool
    {
        $image = $this->create($filename);

        if ($image === false) {
            return false;
        }

        $this->text($image, $size, $angle, $x, $y, $color, $fontfile, $text);

        return $this->save($filename, $image);
    }
}
