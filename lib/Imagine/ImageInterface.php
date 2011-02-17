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

use Imagine\Coordinate\PointInterface;
use Imagine\Coordinate\BoxInterface;
use Imagine\Draw\DrawerInterface;
use Imagine\Gd\Image;
use Imagine\Exception\InvalidArgumentException;
use Imagine\Exception\OutOfBoundsException;
use Imagine\Exception\RuntimeException;

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
     * @param Imagine\Coordinate\PointInterface $start
     * @param Imagine\Coordinate\BoxInterface       $size
     *
     * @throws Imagine\Exception\InvalidArgumentException
     * @throws Imagine\Exception\OutOfBoundsException
     *
     * @return Imagine\ImageInterface
     */
    function crop(PointInterface $start, BoxInterface $size);

    /**
     * Resizes current image and returns self
     *
     * @param Imagine\Coordinate\BoxInterface $size
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
     * @param Imagine\Color $background
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
     * @param Imagine\ImageInterface                 $image
     * @param Imagine\Coordinate\PointInterface $start
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
     * to determine file format, only jpg, jpeg, gif, png, wbmp and xbm
     * The $quality parameter is only relevant for JPEG/JPG images
     *
     * @param string  $path
     * @param integer $quality
     *
     * @throws Imagine\Exception\RuntimeException
     *
     * @return Imagine\ImageInterface
     */
    function save($path, array $options = array());

    /**
     * Outputs the image content
     * The $quality parameter is only relevant for JPEG/JPG images
     *
     * @param string  $format
     * @param integer $quality
     *
     * @throws Imagine\Exception\RuntimeException
     *
     * @return Imagine\ImageInterface
     */
    function show($format, array $options = array());

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
     * @param Imagine\Coordinate\BoxInterface $size
     * @param string                           $mode
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
     * @return Imagine\Coordinate\BoxInterface
     */
    function getSize();
}
