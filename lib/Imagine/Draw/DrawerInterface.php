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

use Imagine\Font;

use Imagine\PointInterface;
use Imagine\BoxInterface;
use Imagine\Color;
use Imagine\Filter\FilterInterface;

interface DrawerInterface
{
    /**
     * Draws an arc on a starting at a given x, y coordinates under a given
     * start and end angles
     *
     * @param Imagine\PointInterface $center
     * @param Imagine\BoxInterface   $size
     * @param integer                $start
     * @param integer                $end
     * @param Imagine\Color          $color
     *
     * @throws Imagine\Exception\RuntimeException
     *
     * @return Imagine\Draw\DrawerInterface
     */
    function arc(PointInterface $center, BoxInterface $size, $start, $end, Color $color);

    /**
     * Same as arc, but also connects end points with a straight line
     *
     * @param Imagine\PointInterface $center
     * @param Imagine\BoxInterface   $size
     * @param integer                $start
     * @param integer                $end
     * @param Imagine\Color          $color
     * @param Boolean                $fill
     *
     * @throws Imagine\Exception\RuntimeException
     *
     * @return Imagine\Draw\DrawerInterface
     */
    function chord(PointInterface $center, BoxInterface $size, $start, $end, Color $color, $fill = false);

    /**
     * Draws and ellipse with center at the given x, y coordinates, and given
     * width and height
     *
     * @param Imagine\PointInterface $center
     * @param Imagine\BoxInterface   $size
     * @param Imagine\Color          $color
     * @param Boolean                $fill
     *
     * @throws Imagine\Exception\RuntimeException
     *
     * @return Imagine\Draw\DrawerInterface
     */
    function ellipse(PointInterface $center, BoxInterface $size, Color $color, $fill = false);

    /**
     * Draws a line from start(x, y) to end(x, y) coordinates
     *
     * @param Imagine\PointInterface $start
     * @param Imagine\PointInterface $end
     * @param Imagine\Color          $outline
     *
     * @return Imagine\Draw\DrawerInterface
     */
    function line(PointInterface $start, PointInterface $end, Color $outline);

    /**
     * Same as arc, but connects end points and the center
     *
     * @param Imagine\PointInterface $center
     * @param Imagine\BoxInterface   $size
     * @param integer                $start
     * @param integer                $end
     * @param Imagine\Color          $color
     * @param Boolean                $fill
     *
     * @throws Imagine\Exception\RuntimeException
     *
     * @return Imagine\Draw\DrawerInterface
     */
    function pieSlice(PointInterface $center, BoxInterface $size, $start, $end, Color $color, $fill = false);

    /**
     * Places a one pixel point at specific coordinates and fills it with
     * specified color
     *
     * @param Imagine\PointInterface $position
     * @param Imagine\Color          $color
     *
     * @throws Imagine\Exception\RuntimeException
     *
     * @return Imagine\Draw\DrawerInterface
     */
    function dot(PointInterface $position, Color $color);

    /**
     * Draws a polygon using array of x, y coordinates. Must contain at least
     * three coordinates
     *
     * @param array         $coordinates
     * @param Imagine\Color $color
     * @param Boolean       $fill
     *
     * @throws Imagine\Exception\RuntimeException
     *
     * @return Imagine\Draw\DrawerInterface
     */
    function polygon(array $coordinates, Color $color, $fill = false);

    /**
     * Annotates image with specified text at a given position starting on the
     * top left of the final text box
     *
     * The rotation is done CW
     *
     * @param string                 $string
     * @param Imagine\Font           $font
     * @param Imagine\PointInterface $position
     * @param integer                $angle
     *
     * @throws Imagine\Exception\RuntimeException
     *
     * @return Imagine\Draw\DrawerInterface
     */
    function text($string, Font $font, PointInterface $position, $angle = 0);
}
