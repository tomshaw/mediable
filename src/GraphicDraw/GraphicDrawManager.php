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
}
