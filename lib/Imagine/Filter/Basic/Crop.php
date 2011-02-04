<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Filter\Basic;

use Imagine\Filter\FilterInterface;
use Imagine\ImageInterface;

class Crop implements FilterInterface
{
    private $x, $y, $width, $height;

    public function __construct($x, $y, $width, $height)
    {
        $this->x      = $x;
        $this->y      = $y;
        $this->width  = $width;
        $this->height = $height;
    }

    public function apply(ImageInterface $image)
    {
        return $image->crop($this->x, $this->y, $this->width, $this->height);
    }
}
