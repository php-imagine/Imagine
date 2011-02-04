<?php

namespace Imagine\Filter\Basic;

use Imagine\Filter\FilterInterface;
use Imagine\ImageInterface;

class Paste implements FilterInterface
{
    private $image;
    private $x, $y;

    public function __construct(ImageInterface $image, $x, $y)
    {
        $this->image = $image;
        $this->x     = $x;
        $this->y     = $y;
    }

    public function apply(ImageInterface $image)
    {
        $image->paste($this->image, $this->x, $this->y);
    }
}
