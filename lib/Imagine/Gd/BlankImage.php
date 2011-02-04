<?php

namespace Imagine\Gd;

use Imagine\Exception\RuntimeException;

final class BlankImage extends Image
{
    public function __construct($width, $height)
    {
        $this->width    = $width;
        $this->height   = $height;
        if (!($this->resource = imagecreatetruecolor($width, $height))) {
            throw new RuntimeException('Create operation failed');
        }
    }
}
