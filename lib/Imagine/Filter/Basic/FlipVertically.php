<?php

namespace Imagine\Filter\Basic;

use Imagine\Filter\FilterInterface;
use Imagine\ImageInterface;

class FlipVertically implements FilterInterface
{
    public function apply(ImageInterface $image)
    {
        return $image->flipVertically();
    }
}
