<?php

namespace Imagine\Filter\Basic;

use Imagine\Filter\FilterInterface;
use Imagine\ImageInterface;

class Resize implements FilterInterface
{
    private $width;
    private $height;

    public function __construct($width, $height)
    {
        $this->width  = $width;
        $this->height = $height;
    }

    public function apply(ImageInterface $image)
    {
        return $image->resize($this->width, $this->height);
    }
}
