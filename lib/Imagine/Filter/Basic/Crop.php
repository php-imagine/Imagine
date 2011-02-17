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

use Imagine\Coordinate\CoordinateInterface;
use Imagine\Coordinate\SizeInterface;
use Imagine\Filter\FilterInterface;
use Imagine\ImageInterface;

class Crop implements FilterInterface
{
    /**
     * @var CoordinateInterface
     */
    private $start;

    /**
     * @var SizeInterface
     */
    private $size;

    /**
     * Constructs a Crop filter with given x, y, coordinates and crop width and
     * height values
     *
     * @param CoordinateInterface $start
     * @param SizeInterface       $size
     */
    public function __construct(CoordinateInterface $start, SizeInterface $size)
    {
        $this->start = $start;
        $this->size  = $size;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Filter.FilterInterface::apply()
     */
    public function apply(ImageInterface $image)
    {
        return $image->crop($this->start, $this->size);
    }
}
