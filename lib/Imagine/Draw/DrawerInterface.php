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

use Imagine\Coordinate\CoordinateInterface;
use Imagine\Coordinate\SizeInterface;
use Imagine\Color;
use Imagine\Filter\FilterInterface;

interface DrawerInterface
{
    /**
     * Draws an arc on a starting at a given x, y coordinates under a given
     * start and end angles
     *
     * @param Imagine\Coordinate\CoordinateInterface $center
     * @param Imagine\Coordinate\SizeInterface       $size
     * @param integer                               $start
     * @param integer                               $end
     * @param Imagine\Color                         $color
     *
     * @return Imagine\Draw\DrawerInterface
     */
    function arc(CoordinateInterface $center, SizeInterface $size, $start, $end, Color $color);

    /**
     * Same as arc, but also connects end points with a straight line
     *
     * @param Imagine\Coordinate\CoordinateInterface $center
     * @param Imagine\Coordinate\SizeInterface       $size
     * @param integer                               $start
     * @param integer                               $end
     * @param Imagine\Color                         $color
     * @param Boolean                               $fill
     *
     * @return Imagine\Draw\DrawerInterface
     */
    function chord(CoordinateInterface $center, SizeInterface $size, $start, $end, Color $color, $fill = false);

    /**
     * Draws and ellipse with center at the given x, y coordinates, and given
     * width and height
     *
     * @param Imagine\Coordinate\CoordinateInterface $center
     * @param Imagine\Coordinate\SizeInterface       $size
     * @param Imagine\Color                         $color
     * @param Boolean                               $fill
     *
     * @return Imagine\Draw\DrawerInterface
     */
    function ellipse(CoordinateInterface $center, SizeInterface $size, Color $color, $fill = false);

    /**
     * Draws a line from x1, y1 to x2, y2 coordinates
     *
     * @param Imagine\Coordinate\CoordinateInterface $start
     * @param Imagine\Coordinate\CoordinateInterface $end
     * @param Imagine\Color                         $outline
     *
     * @return Imagine\Draw\DrawerInterface
     */
    function line(CoordinateInterface $start, CoordinateInterface $end, Color $outline);

    /**
     * Same as arc, but connects end points and the center
     *
     * @param Imagine\Coordinate\CoordinateInterface $center
     * @param Imagine\Coordinate\SizeInterface       $size
     * @param integer                               $start
     * @param integer                               $end
     * @param Imagine\Color                         $color
     * @param Boolean                               $fill
     *
     * @return Imagine\Draw\DrawerInterface
     */
    function pieSlice(CoordinateInterface $center, SizeInterface $size, $start, $end, Color $color, $fill = false);

    /**
     * Places a one pixel point at specific coordinates and fills it with
     * specified color
     *
     * @param Imagine\Coordinate\CoordinateInterface $position
     * @param Imagine\Color                         $color
     *
     * @return Imagine\Draw\DrawerInterface
     */
    function dot(CoordinateInterface $position, Color $color);

    /**
     * Draws a polygon using array of x, y coordinates. Must contain at least
     * three coordinates
     *
     * @param array         $coordinates
     * @param Imagine\Color $color
     * @param Boolean       $fill
     *
     * @return Imagine\Draw\DrawerInterface
     */
    function polygon(array $coordinates, Color $color, $fill = false);

//    function text($x, $y, Font $font, Color $color);
}
