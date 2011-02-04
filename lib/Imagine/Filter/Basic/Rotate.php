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

use Imagine\Color;
use Imagine\ImageInterface;
use Imagine\Filter\FilterInterface;

class Rotate implements FilterInterface
{
    private $angle;
    private $background;

    public function __construct($angle, Color $background = null)
    {
        $this->angle      = $angle;
        $this->background = $background;
    }

    public function apply(ImageInterface $image)
    {
        return $image->rotate($this->angle, $this->background);
    }
}
