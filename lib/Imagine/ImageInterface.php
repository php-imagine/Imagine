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

use Imagine\Exception\InvalidArgumentException;
use Imagine\Exception\OutOfBoundsException;
use Imagine\Exception\RuntimeException;

interface ImageInterface
{
    /**
     * Copies current source image into a new ImageInterface instance
     *
     * @throws RuntimeException
     *
     * @return ImageInterface
     */
    function copy();

    /**
     * Crops a specified box out of the source image (modifies the source image)
     * Returns cropped self
     *
     * @param integer $x
     * @param integer $y
     * @param integer $width
     * @param integer $height
     *
     * @throws InvalidArgumentException
     * @throws OutOfBoundsException
     *
     * @return ImageInterface
     */
    function crop($x, $y, $width, $height);

    /**
     * Resizes current image and returns self
     *
     * @param integer $width
     * @param integer $height
     *
     * @return ImageInterface
     */
    function resize($width, $height);

    /**
     * Rotates an image at the given angle.
     * Optional $background can be used to specify the fill color of the empty
     * area of rotated image.
     *
     * @param integer $angle
     * @param Color   $background
     *
     * @throws RuntimeException
     *
     * @return ImageInterface
     */
    function rotate($angle, Color $background = null);

    /**
     * Pastes an image into a parent image
     * Throws exceptions if image exceeds parent image borders or if paste
     * operation fails
     *
     * Returns source image
     *
     * @param ImageInterface $image
     * @param integer        $x
     * @param integer        $y
     *
     * @throws InvalidArgumentException
     * @throws OutOfBoundsException
     * @throws RuntimeException
     *
     * @return ImageInterface
     */
    function paste(ImageInterface $image, $x, $y);

    /**
     * Saves the image at a specified path, the target file extension is used
     * to determine file format, only jpg, jpeg, gif, png, wbmp and xbm
     * The $quality parameter is only relevant for JPEG/JPG images
     *
     * @param string  $path
     * @param integer $quality
     *
     * @throws RuntimeException
     *
     * @return ImageInterface
     */
    function save($path, array $options = array());

    /**
     * Outputs the image content
     * The $quality parameter is only relevant for JPEG/JPG images
     *
     * @param string $format
     * @param integer $quality
     *
     * @throws RuntimeException
     */
    function show($format, array $options = array());

    /**
     * Flips current image using horizontal axis
     *
     * @throws RuntimeException
     *
     * @return ImageInterface
     */
    function flipHorizontally();

    /**
     * Flips current image using vertical axis
     *
     * @throws RuntimeException
     *
     * @return ImageInterface
     */
    function flipVertically();
}
