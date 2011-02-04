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
        return $image->paste($this->image, $this->x, $this->y);
    }
}
