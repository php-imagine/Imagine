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
use Imagine\Image\Fill\FillInterface;

interface ManipulatorInterface
{
    const THUMBNAIL_INSET    = 'inset';
    const THUMBNAIL_OUTBOUND = 'outbound';

    /**
     * Copies current source image into a new ImageInterface instance
     *
     * @throws Imagine\Exception\RuntimeException
     *
     * @return Imagine\Image\ManipulatorInterface
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
     * @return Imagine\Image\ManipulatorInterface
     */
    function crop(PointInterface $start, BoxInterface $size);

    /**
     * Resizes current image and returns self
     *
     * @param Imagine\Image\BoxInterface $size
     *
     * @throws Imagine\Exception\RuntimeException
     *
     * @return Imagine\Image\ManipulatorInterface
     */
    function resize(BoxInterface $size);

    /**
     * Rotates an image at the given angle.
     * Optional $background can be used to specify the fill color of the empty
     * area of rotated image.
     *
     * @param integer             $angle
     * @param Imagine\Image\Color $background
     *
     * @throws Imagine\Exception\RuntimeException
     *
     * @return Imagine\Image\ManipulatorInterface
     */
    function rotate($angle, Color $background = null);

    /**
     * Pastes an image into a parent image
     * Throws exceptions if image exceeds parent image borders or if paste
     * operation fails
     *
     * Returns source image
     *
     * @param Imagine\Image\ImageInterface $image
     * @param Imagine\Image\PointInterface $start
     *
     * @throws Imagine\Exception\InvalidArgumentException
     * @throws Imagine\Exception\OutOfBoundsException
     * @throws Imagine\Exception\RuntimeException
     *
     * @return Imagine\Image\ManipulatorInterface
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
     * @return Imagine\Image\ManipulatorInterface
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
     * @return Imagine\Image\ManipulatorInterface
     */
    function show($format, array $options = array());

    /**
     * Flips current image using horizontal axis
     *
     * @throws Imagine\Exception\RuntimeException
     *
     * @return Imagine\Image\ManipulatorInterface
     */
    function flipHorizontally();

    /**
     * Flips current image using vertical axis
     *
     * @throws Imagine\Exception\RuntimeException
     *
     * @return Imagine\Image\ManipulatorInterface
     */
    function flipVertically();

    /**
     * Generates a thumbnail from a current image
     * Returns it as a new image, doesn't modify the current image
     *
     * @param Imagine\Image\BoxInterface $size
     * @param string                     $mode
     *
     * @throws Imagine\Exception\RuntimeException
     *
     * @return Imagine\Image\ManipulatorInterface
     */
    function thumbnail(BoxInterface $size, $mode = self::THUMBNAIL_INSET);

    /**
     * Applies a given mask to current image's alpha channel
     *
     * @param Imagine\Image\ImageInterface $mask
     *
     * @return Imagine\Image\ManipulatorInterface
     */
    function applyMask(ImageInterface $mask);

    /**
     * Fills image with provided filling, by replacing each pixel's color in
     * the current image with corresponding color from FillInterface, and
     * returns modified image
     *
     * @param Imagine\Image\Fill\FillInterface $fill
     *
     * @return Imagine\Image\ManipulatorInterface
     */
    function fill(FillInterface $fill);
}
