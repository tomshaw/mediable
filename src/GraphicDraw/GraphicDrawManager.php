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
}
