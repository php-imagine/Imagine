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

use Imagine\Point;
use Imagine\Filter\FilterInterface;
use Imagine\ImageInterface;

class Crop implements FilterInterface
{
    private $start, $width, $height;

    /**
     * Constructs a Crop filter with given x, y, coordinates and crop width and
     * height values
     *
     * @param Point   $start
     * @param integer $width
     * @param integer $height
     */
    public function __construct(Point $start, $width, $height)
    {
        $this->start  = $start;
        $this->width  = $width;
        $this->height = $height;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Filter.FilterInterface::apply()
     */
    public function apply(ImageInterface $image)
    {
        return $image->crop($this->start, $this->width, $this->height);
    }
}
