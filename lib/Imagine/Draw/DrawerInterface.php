<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Draw;

use Imagine\Cartesian\CoordinateInterface;
use Imagine\Cartesian\SizeInterface;
use Imagine\Color;
use Imagine\Filter\FilterInterface;

interface DrawerInterface
{
    /**
     * Draws an arc on a starting at a given x, y coordinates under a given
     * start and end angles
     *
     * @param CoordinateInterface $center
     * @param SizeInterface       $size
     * @param integer             $start
     * @param integer             $end
     * @param Color               $color
     *
     * @return DrawerInterface
     */
    function arc(CoordinateInterface $center, SizeInterface $size, $start, $end, Color $color);

    /**
     * Same as arc, but also connects end points with a straight line
     *
     * @param CoordinateInterface $center
     * @param integer             $width
     * @param integer             $height
     * @param integer             $start
     * @param Color               $color
     * @param integer             $end
     * @param Boolean             $fill
     *
     * @return DrawerInterface
     */
    function chord(CoordinateInterface $center, $width, $height, $start, $end, Color $color, $fill = false);

    /**
     * Draws and ellipse with center at the given x, y coordinates, and given
     * width and height
     *
     * @param CoordinateInterface $center
     * @param integer             $width
     * @param integer             $height
     * @param Color               $color
     * @param Boolean             $fill
     *
     * @return DrawerInterface
     */
    function ellipse(CoordinateInterface $center, $width, $height, Color $color, $fill = false);

    /**
     * Draws a line from x1, y1 to x2, y2 coordinates
     *
     * @param CoordinateInterface $start
     * @param CoordinateInterface $end
     * @param Color               $outline
     *
     * @return DrawerInterface
     */
    function line(CoordinateInterface $start, CoordinateInterface $end, Color $outline);

    /**
     * Same as arc, but connects end points and the center
     *
     * @param CoordinateInterface $center
     * @param integer             $width
     * @param integer             $height
     * @param integer             $start
     * @param integer             $end
     * @param Color               $color
     * @param Boolean             $fill
     *
     * @return DrawerInterface
     */
    function pieSlice(CoordinateInterface $center, $width, $height, $start, $end, Color $color, $fill = false);

    /**
     * Places a one pixel point at specific coordinates and fills it with
     * specified color
     *
     * @param CoordinateInterface $position
     * @param Color               $color
     *
     * @return DrawerInterface
     */
    function dot(CoordinateInterface $position, Color $color);

    /**
     * Draws a polygon using array of x, y coordinates. Must contain at least
     * three coordinates
     *
     * @param array   $coordinates
     * @param Color   $color
     * @param Boolean $fill
     *
     * @return DrawerInterface
     */
    function polygon(array $coordinates, Color $color, $fill = false);

//    function text($x, $y, Font $font, Color $color);
}
