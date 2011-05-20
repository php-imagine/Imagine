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
    const TOP    = 'top';
    const MIDDLE = 'middle';
    const BOTTOM = 'bottom';
    const LEFT   = 'left';
    const CENTER = 'center';
    const RIGHT  = 'right';

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
     * Creates new BoxInterface, adding given size to both sides
     *
     * @param integer $size
     */
    function increase($size);

    /**
     * Checks whether current box can fit given box at a given start position,
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
     * Gets point at the specified position in a box, e.g for a 100x100 box
     *
     * The order of parameters doesn't matter
     *
     * top,    left   - (0,0)
     * bottom, left   - (0,100)
     * bottom, right  - (100,100)
     * middle, center - (50,50)
     * middle, right  - (100,50)
     *
     * @param string $a
     * @param string $b
     *
     * @return Imagine\Image\PointInterface
     */
    function position($a, $b);

    /**
     * Returns a string representation of the current box
     *
     * @return string
     */
    function __toString();

    /**
     * Resizes box to given width, constraining proportions and returns the new box
     *
     * @param integer $width
     *
     * @return Imagine\Image\BoxInterface
     */
    function widen($width);

    /**
     * Resizes box to given height, constraining proportions and returns the new box
     *
     * @param integer $height
     *
     * @return Imagine\Image\BoxInterface
     */
    function heighten($height);
}
