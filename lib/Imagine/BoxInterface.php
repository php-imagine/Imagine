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
     * @return Imagine\BoxInterface
     */
    function scale($ratio);

    /**
     * Creats new BoxInterface, adding given size to both sides
     *
     * @param integer $size
     */
    function increase($size);

    /**
     * Checks whether curret box can fit given box at a given start position,
     * start position defaults to top left corner xy(0,0)
     *
     * @param Imagine\BoxInterface       $box
     * @param Imagine\PointInterface $start
     *
     * @return Boolean
     */
    function contains(BoxInterface $box, PointInterface $start = null);

    /**
     * Returns a string representation of the current box
     *
     * @return string
     */
    function __toString();
}
