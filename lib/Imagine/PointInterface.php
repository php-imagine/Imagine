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

interface PointInterface
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

    /**
     * Checks if current coordinate is inside a given bo
     *
     * @param Imagine\BoxInterface $box
     *
     * @return Boolean
     */
    function in(BoxInterface $box);

    /**
     * Gets a string representation for the current point
     *
     * @return string
     */
    function __toString();
}
