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

interface CoordinateInterface
{
    /**
     * Gets points x coordinate
     *
     * @return integer
     */
    function getX();

    /**
     * Gets points y coordinate
     *
     * @return integer
     */
    function getY();
}
