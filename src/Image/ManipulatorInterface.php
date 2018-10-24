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

use Imagine\Image\Fill\FillInterface;
use Imagine\Image\Palette\Color\ColorInterface;

/**
 * The manipulator interface.
 */
interface ManipulatorInterface
{
    /**
     * The original image is scaled so it is fully contained within the thumbnail dimensions (the image width/height ratio doesn't change).
     *
     * @var int
     */
    const THUMBNAIL_INSET = 0x00000001;

    /**
     * The thumbnail is scaled so that its smallest side equals the length of the corresponding side in the original image (the width or the height are cropped).
     *
     * @var int
     */
    const THUMBNAIL_OUTBOUND = 0x00000002;

    /**
     * Allow upscaling the image if it's smaller than the wanted thumbnail size.
     *
     * @var int
     */
    const THUMBNAIL_FLAG_UPSCALE = 0x00010000;

    /**
     * Instead of creating a new image instance, the thumbnail method modifies the original image (saving memory.
     *
     * @var int
     */
    const THUMBNAIL_FLAG_NOCLONE = 0x00020000;

    /**
     * Copies current source image into a new ImageInterface instance.
     *
     * @throws \Imagine\Exception\RuntimeException
     *
     * @return static
     */
    public function copy();

    /**
     * Crops a specified box out of the source image (modifies the source image)
     * Returns cropped self.
     *
     * @param \Imagine\Image\PointInterface $start
     * @param \Imagine\Image\BoxInterface $size
     *
     * @throws \Imagine\Exception\OutOfBoundsException
     * @throws \Imagine\Exception\RuntimeException
     *
     * @return $this
     */
    public function crop(PointInterface $start, BoxInterface $size);

    /**
     * Resizes current image and returns self.
     *
     * @param \Imagine\Image\BoxInterface $size
     * @param string $filter
     *
     * @throws \Imagine\Exception\RuntimeException
     *
     * @return $this
     */
    public function resize(BoxInterface $size, $filter = ImageInterface::FILTER_UNDEFINED);

    /**
     * Rotates an image at the given angle.
     * Optional $background can be used to specify the fill color of the empty
     * area of rotated image.
     *
     * @param int $angle
     * @param \Imagine\Image\Palette\Color\ColorInterface $background
     *
     * @throws \Imagine\Exception\RuntimeException
     *
     * @return $this
     */
    public function rotate($angle, ColorInterface $background = null);

    /**
     * Pastes an image into a parent image
     * Throws exceptions if image exceeds parent image borders or if paste
     * operation fails.
     *
     * Returns source image
     *
     * @param \Imagine\Image\ImageInterface $image
     * @param \Imagine\Image\PointInterface $start
     * @param int $alpha How to paste the image, from 0 (fully transparent) to 100 (fully opaque)
     *
     * @throws \Imagine\Exception\InvalidArgumentException
     * @throws \Imagine\Exception\RuntimeException
     *
     * @return $this
     */
    public function paste(ImageInterface $image, PointInterface $start, $alpha = 100);

    /**
     * Saves the image at a specified path, the target file extension is used
     * to determine file format, only jpg, jpeg, gif, png, wbmp, xbm, webp and bmp are
     * supported.
     * Please remark that bmp is supported by the GD driver only since PHP 7.2.
     *
     * @param string $path
     * @param array $options
     *
     * @throws \Imagine\Exception\RuntimeException
     *
     * @return $this
     */
    public function save($path = null, array $options = array());

    /**
     * Outputs the image content.
     *
     * @param string $format
     * @param array $options
     *
     * @throws \Imagine\Exception\RuntimeException
     *
     * @return $this
     */
    public function show($format, array $options = array());

    /**
     * Flips current image using vertical axis.
     *
     * @throws \Imagine\Exception\RuntimeException
     *
     * @return $this
     */
    public function flipHorizontally();

    /**
     * Flips current image using horizontal axis.
     *
     * @throws \Imagine\Exception\RuntimeException
     *
     * @return $this
     */
    public function flipVertically();

    /**
     * Remove all profiles and comments.
     *
     * @throws \Imagine\Exception\RuntimeException
     *
     * @return $this
     */
    public function strip();

    /**
     * Generates a thumbnail from a current image
     * Returns it as a new image without modifying the current image unless the THUMBNAIL_FLAG_NOCLONE flag is specified.
     *
     * @param \Imagine\Image\BoxInterface $size
     * @param int|string $settings One or more of the ManipulatorInterface::THUMBNAIL_ flags (joined with |). It may be a string for backward compatibility with old constant values that were strings.
     * @param string $filter The filter to use for resizing, one of ImageInterface::FILTER_*
     *
     * @throws \Imagine\Exception\RuntimeException
     *
     * @return static
     */
    public function thumbnail(BoxInterface $size, $settings = self::THUMBNAIL_INSET, $filter = ImageInterface::FILTER_UNDEFINED);

    /**
     * Applies a given mask to current image's alpha channel.
     *
     * @param \Imagine\Image\ImageInterface $mask
     *
     * @return $this
     */
    public function applyMask(ImageInterface $mask);

    /**
     * Fills image with provided filling, by replacing each pixel's color in
     * the current image with corresponding color from FillInterface, and
     * returns modified image.
     *
     * @param \Imagine\Image\Fill\FillInterface $fill
     *
     * @return $this
     */
    public function fill(FillInterface $fill);
}
