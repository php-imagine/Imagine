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

use Imagine\Exception\InvalidArgumentException;
use Imagine\Exception\OutOfBoundsException;
use Imagine\Exception\RuntimeException;

interface ImageInterface extends ManipulatorInterface
{
    /**
     * Returns the image content as a binary string
     *
     * @param string $format
     * @param array  $options
     *
     * @throws Imagine\Exception\RuntimeException
     *
     * @return string binary
     */
    function get($format, array $options = array());

    /**
     * Returns the image content as a PNG binary string
     *
     * @param string $format
     * @param array  $options
     *
     * @throws Imagine\Exception\RuntimeException
     *
     * @return string binary
     */
    function __toString();

    /**
     * Instantiates and returns a DrawerInterface instance for image drawing
     *
     * @return Imagine\Draw\DrawerInterface
     */
    function draw();

    /**
     * Returns current image size
     *
     * @return Imagine\Image\BoxInterface
     */
    function getSize();

    /**
     * Transforms creates a grayscale mask from current image, returns a new
     * image, while keeping the existing image unmodified
     *
     * @return Imagine\Image\ImageInterface
     */
    function mask();

    /**
     * Returns array of image colors as Imagine\Image\Color instances
     *
     * @return array
     */
    function histogram();

    /**
     * Returns color at specified positions of current image
     *
     * @param Imagine\Image\PointInterface $point
     *
     * @throws Imagine\Exception\RuntimeException
     *
     * @return Imagine\Image\Color
     */
    function getColorAt(PointInterface $point);
}
