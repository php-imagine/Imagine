<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Image;

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
     * @return Imagine\Image\BoxInterface
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
     * @param Imagine\Image\BoxInterface       $box
     * @param Imagine\Image\PointInterface $start
     *
     * @return Boolean
     */
    function contains(BoxInterface $box, PointInterface $start = null);

    /**
     * Gets current box square, useful for getting total number of pixels in a
     * given box
     *
     * @return integer
     */
    function square();

    /**
     * Returns a string representation of the current box
     *
     * @return string
     */
    function __toString();
}
