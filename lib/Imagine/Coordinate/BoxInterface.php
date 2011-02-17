<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Coordinate;

interface BoxInterface
{
    /**
     * Gets current image height
     *
     * @return integer
     */
    function getHeight();

    /**
     * Gets current image width
     *
     * @return integer
     */
    function getWidth();

    /**
     * Creates new BoxInterface instance with ratios applied to both sides
     *
     * @param integer $ratio
     *
     * @return Imagine\Coordinate\BoxInterface
     */
    function scale($ratio);

    /**
     * Checks whether curret box can fit given box at a given start position,
     * start position defaults to top left corner xy(0,0)
     *
     * @param Imagine\Coordinate\BoxInterface       $box
     * @param Imagine\Coordinate\PointInterface $start
     *
     * @return Boolean
     */
    function contains(BoxInterface $box, PointInterface $start = null);
}
