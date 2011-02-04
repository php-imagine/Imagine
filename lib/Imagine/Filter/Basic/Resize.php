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
