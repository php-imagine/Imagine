<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Cartesian;

final class Coordinate implements CoordinateInterface
{
    private $x;
    private $y;

    /**
     * Constructs a point of coordinates
     *
     * @param integer $x
     * @param integer $y
     */
    public function __construct($x, $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Cartesian.CoordinateInterface::getX()
     */
    public function getX()
    {
        return $this->x;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Cartesian.CoordinateInterface::getY()
     */
    public function getY()
    {
        return $this->y;
    }
}
