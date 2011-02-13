<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine;

final class Point
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
     * Gets points x coordinate
     *
     * @return integer
     */
    public function getX()
    {
        return $this->x;
    }

    /**
     * Gets points y coordinate
     *
     * @return integer
     */
    public function getY()
    {
        return $this->y;
    }
}
