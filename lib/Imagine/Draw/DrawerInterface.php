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

use Imagine\Color;
use Imagine\Filter\FilterInterface;

interface DrawerInterface
{
    /**
     * Draws an arc on a starting at a given x, y coordinates under a given
     * start and end angles
     *
     * @param integer $x
     * @param integer $y
     * @param integer $width
     * @param integer $height
     * @param integer $start
     * @param integer $end
     * @param Color   $outline
     *
     * @return DrawerInterface
     */
    function arc($x, $y, $width, $height, $start, $end, Color $outline);

    /**
     * Same as arc, but also connects end points with a straight line
     *
     * @param integer $x
     * @param integer $y
     * @param integer $width
     * @param integer $height
     * @param integer $start
     * @param integer $end
     * @param Color   $outline
     * @param Boolean $fill
     *
     * @return DrawerInterface
     */
    function chord($x, $y, $width, $height, $start, $end, Color $outline, $fill = false);

    /**
     * Draws and ellipse with center at the given x, y coordinates, and given
     * width and height
     *
     * @param integer $x
     * @param integer $y
     * @param integer $width
     * @param integer $height
     * @param Color   $outline
     * @param Boolean $fill
     *
     * @return DrawerInterface
     */
    function ellipse($x, $y, $width, $height, Color $outline, $fill = false);

    /**
     * Draws a line from x1, y1 to x2, y2 coordinates
     *
     * @param integer $x1
     * @param integer $y1
     * @param integer $x2
     * @param integer $y2
     * @param Color   $outline
     *
     * @return DrawerInterface
     */
    function line($x1, $y1, $x2, $y2, Color $outline);

    /**
     * Same as arc, but connects end points and the center
     *
     * @param integer $x
     * @param integer $y
     * @param integer $width
     * @param integer $height
     * @param integer $start
     * @param integer $end
     * @param Color   $outline
     * @param Boolean $fill
     *
     * @return DrawerInterface
     */
    function pieSlice($x, $y, $width, $height, $start, $end, Color $outline, $fill = false);

    /**
     * Places a one pixel point at specific coordinates and fills it with
     * specified color
     *
     * @param integer $x
     * @param integer $y
     * @param Color   $color
     *
     * @return DrawerInterface
     */
    function point($x, $y, Color $color);

    /**
     * Draws a polygon using array of x, y coordinates. Must contain at least
     * three coordinates
     *
     * @param array   $coordinates
     * @param Color   $outline
     * @param Boolean $fill
     *
     * @return DrawerInterface
     */
    function polygon(array $coordinates, Color $outline, $fill = false);

//    function text($x, $y, Font $font, Color $color);
}
