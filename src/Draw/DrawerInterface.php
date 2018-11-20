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

use Imagine\Image\AbstractFont;
use Imagine\Image\BoxInterface;
use Imagine\Image\Palette\Color\ColorInterface;
use Imagine\Image\PointInterface;

/**
 * Interface for the drawer.
 */
interface DrawerInterface
{
    /**
     * Draws an arc on a starting at a given x, y coordinates under a given
     * start and end angles.
     *
     * @param \Imagine\Image\PointInterface $center
     * @param \Imagine\Image\BoxInterface $size
     * @param int $start
     * @param int $end
     * @param \Imagine\Image\Palette\Color\ColorInterface $color
     * @param int $thickness
     *
     * @throws \Imagine\Exception\RuntimeException
     *
     * @return $this
     */
    public function arc(PointInterface $center, BoxInterface $size, $start, $end, ColorInterface $color, $thickness = 1);

    /**
     * Same as arc, but also connects end points with a straight line.
     *
     * @param \Imagine\Image\PointInterface $center
     * @param \Imagine\Image\BoxInterface $size
     * @param int $start
     * @param int $end
     * @param \Imagine\Image\Palette\Color\ColorInterface $color
     * @param bool $fill
     * @param int $thickness
     *
     * @throws \Imagine\Exception\RuntimeException
     *
     * @return $this
     */
    public function chord(PointInterface $center, BoxInterface $size, $start, $end, ColorInterface $color, $fill = false, $thickness = 1);

    /**
     * Draws and circle with center at the given x, y coordinates, and given radius.
     *
     * @param \Imagine\Image\PointInterface $center
     * @param int $radius
     * @param \Imagine\Image\Palette\Color\ColorInterface $color
     * @param bool $fill
     * @param int $thickness
     *
     * @throws \Imagine\Exception\RuntimeException
     *
     * @return $this
     */
    public function circle(PointInterface $center, $radius, ColorInterface $color, $fill = false, $thickness = 1);

    /**
     * Draws and ellipse with center at the given x, y coordinates, and given width and height.
     *
     * @param \Imagine\Image\PointInterface $center
     * @param \Imagine\Image\BoxInterface $size
     * @param \Imagine\Image\Palette\Color\ColorInterface $color
     * @param bool $fill
     * @param int $thickness
     *
     * @throws \Imagine\Exception\RuntimeException
     *
     * @return $this
     */
    public function ellipse(PointInterface $center, BoxInterface $size, ColorInterface $color, $fill = false, $thickness = 1);

    /**
     * Draws a line from start(x, y) to end(x, y) coordinates.
     *
     * @param \Imagine\Image\PointInterface $start
     * @param \Imagine\Image\PointInterface $end
     * @param \Imagine\Image\Palette\Color\ColorInterface $outline
     * @param int $thickness
     *
     * @return $this
     */
    public function line(PointInterface $start, PointInterface $end, ColorInterface $outline, $thickness = 1);

    /**
     * Same as arc, but connects end points and the center.
     *
     * @param \Imagine\Image\PointInterface $center
     * @param \Imagine\Image\BoxInterface $size
     * @param int $start
     * @param int $end
     * @param \Imagine\Image\Palette\Color\ColorInterface $color
     * @param bool $fill
     * @param int $thickness
     *
     * @throws \Imagine\Exception\RuntimeException
     *
     * @return $this
     */
    public function pieSlice(PointInterface $center, BoxInterface $size, $start, $end, ColorInterface $color, $fill = false, $thickness = 1);

    /**
     * Places a one pixel point at specific coordinates and fills it with
     * specified color.
     *
     * @param \Imagine\Image\PointInterface $position
     * @param \Imagine\Image\Palette\Color\ColorInterface $color
     *
     * @throws \Imagine\Exception\RuntimeException
     *
     * @return $this
     */
    public function dot(PointInterface $position, ColorInterface $color);

    /**
     * Draws a rectangle from left, top(x, y) to right, bottom(x, y) coordinates.
     *
     * @param \Imagine\Image\PointInterface $leftTop
     * @param \Imagine\Image\PointInterface $rightBottom
     * @param \Imagine\Image\Palette\Color\ColorInterface $color
     * @param bool $fill
     * @param int $thickness
     *
     * @throws \Imagine\Exception\RuntimeException
     *
     * @return $this
     */
    public function rectangle(PointInterface $leftTop, PointInterface $rightBottom, ColorInterface $color, $fill = false, $thickness = 1);

    /**
     * Draws a polygon using array of x, y coordinates. Must contain at least three coordinates.
     *
     * @param \Imagine\Image\PointInterface[] $coordinates
     * @param \Imagine\Image\Palette\Color\ColorInterface $color
     * @param bool $fill
     * @param int $thickness
     *
     * @throws \Imagine\Exception\RuntimeException
     *
     * @return $this
     */
    public function polygon(array $coordinates, ColorInterface $color, $fill = false, $thickness = 1);

    /**
     * Annotates image with specified text at a given position starting on the top left of the final text box.
     *
     * The rotation is done CW
     *
     * @param string $string
     * @param \Imagine\Image\AbstractFont $font
     * @param \Imagine\Image\PointInterface $position
     * @param int $angle
     * @param int $width
     *
     * @throws \Imagine\Exception\RuntimeException
     *
     * @return $this
     */
    public function text($string, AbstractFont $font, PointInterface $position, $angle = 0, $width = null);
}
