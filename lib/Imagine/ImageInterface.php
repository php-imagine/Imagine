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

use Imagine\Draw\DrawerInterface;
use Imagine\Exception\InvalidArgumentException;
use Imagine\Exception\OutOfBoundsException;
use Imagine\Exception\RuntimeException;
use Imagine\Fill\FillInterface;
use Imagine\Image\BoxInterface;
use Imagine\Image\Color;
use Imagine\Image\PointInterface;

interface ImageInterface
{
    const THUMBNAIL_INSET    = 'inset';
    const THUMBNAIL_OUTBOUND = 'outbound';

    /**
     * Copies current source image into a new ImageInterface instance
     *
     * @throws Imagine\Exception\RuntimeException
     *
     * @return Imagine\ImageInterface
     */
    function copy();

    /**
     * Crops a specified box out of the source image (modifies the source image)
     * Returns cropped self
     *
     * @param Imagine\Image\PointInterface $start
     * @param Imagine\Image\BoxInterface   $size
     *
     * @throws Imagine\Exception\OutOfBoundsException
     * @throws Imagine\Exception\RuntimeException
     *
     * @return Imagine\ImageInterface
     */
    function crop(PointInterface $start, BoxInterface $size);

    /**
     * Resizes current image and returns self
     *
     * @param Imagine\Image\BoxInterface $size
     *
     * @throws Imagine\Exception\RuntimeException
     *
     * @return Imagine\ImageInterface
     */
    function resize(BoxInterface $size);

    /**
     * Rotates an image at the given angle.
     * Optional $background can be used to specify the fill color of the empty
     * area of rotated image.
     *
     * @param integer       $angle
     * @param Imagine\Image\Color $background
     *
     * @throws Imagine\Exception\RuntimeException
     *
     * @return Imagine\ImageInterface
     */
    function rotate($angle, Color $background = null);

    /**
     * Pastes an image into a parent image
     * Throws exceptions if image exceeds parent image borders or if paste
     * operation fails
     *
     * Returns source image
     *
     * @param Imagine\ImageInterface $image
     * @param Imagine\Image\PointInterface $start
     *
     * @throws Imagine\Exception\InvalidArgumentException
     * @throws Imagine\Exception\OutOfBoundsException
     * @throws Imagine\Exception\RuntimeException
     *
     * @return Imagine\ImageInterface
     */
    function paste(ImageInterface $image, PointInterface $start);

    /**
     * Saves the image at a specified path, the target file extension is used
     * to determine file format, only jpg, jpeg, gif, png, wbmp and xbm are
     * supported
     *
     * @param string $path
     * @param array  $options
     *
     * @throws Imagine\Exception\RuntimeException
     *
     * @return Imagine\ImageInterface
     */
    function save($path, array $options = array());

    /**
     * Outputs the image content
     *
     * @param string $format
     * @param array  $options
     *
     * @throws Imagine\Exception\RuntimeException
     *
     * @return Imagine\ImageInterface
     */
    function show($format, array $options = array());

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
     * Flips current image using horizontal axis
     *
     * @throws Imagine\Exception\RuntimeException
     *
     * @return Imagine\ImageInterface
     */
    function flipHorizontally();

    /**
     * Flips current image using vertical axis
     *
     * @throws Imagine\Exception\RuntimeException
     *
     * @return Imagine\ImageInterface
     */
    function flipVertically();

    /**
     * Generates a thumbnail from a current image
     * Returns it as a new image, doesn't modify the current image
     *
     * @param Imagine\Image\BoxInterface $size
     * @param string               $mode
     *
     * @throws Imagine\Exception\RuntimeException
     *
     * @return Imagine\ImageInterface
     */
    function thumbnail(BoxInterface $size, $mode = self::THUMBNAIL_INSET);

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
     * Applies a given mask to current image's alpha channel
     *
     * @param Imagine\ImageInterface $mask
     *
     * @return Imagine\ImageInterface
     */
    function applyMask(ImageInterface $mask);

    /**
     * Transforms creates a grayscale mask from current image, returns a new
     * image, while keeping the existing image unmodified
     *
     * @return Imagine\ImageInterface
     */
    function mask();

    /**
     * Fills image with provided filling, by replacing each pixel's color in
     * the current image with corresponding color from FillInterface, and
     * returns modified image
     *
     * @param Imagine\Fill\FillInterface $fill
     *
     * @return Imagine\ImageInterface
     */
    function fill(FillInterface $fill);

    /**
     * Returns array of image colors as Imagine\Image\Color instances
     *
     * @return array
     */
    function histogram();
}
