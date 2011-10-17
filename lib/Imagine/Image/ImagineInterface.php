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
use Imagine\Exception\RuntimeException;

interface ImagineInterface
{
    /**
     * Creates a new empty image with an optional background color
     *
     * @param Imagine\Image\BoxInterface $size
     * @param Imagine\Image\Color        $color
     *
     * @throws Imagine\Exception\InvalidArgumentException
     * @throws Imagine\Exception\RuntimeException
     *
     * @return Imagine\Image\ImageInterface
     */
    function create(BoxInterface $size, Color $color = null);

    /**
     * Opens an existing image from $path
     *
     * @param string $path
     *
     * @throws Imagine\Exception\RuntimeException
     *
     * @return Imagine\Image\ImageInterface
     */
    function open($path);

    /**
     * Loads an image from a binary $string
     *
     * @param string $string
     *
     * @throws Imagine\Exception\RuntimeException
     *
     * @return Imagine\Image\ImageInterface
     */
    function load($string);

    /**
     * Loads an image from a resource $resource
     *
     * @param resource $resource
     *
     * @throws Imagine\Exception\RuntimeException
     *
     * @return Imagine\Image\ImageInterface
     */
    function read($resource);

    /**
     * Constructs a font with specified $file, $size and $color
     *
     * The font size is to be specified in points (e.g. 10pt means 10)
     *
     * @param string              $file
     * @param integer             $size
     * @param Imagine\Image\Color $color
     *
     * @return Imagine\Image\AbstractFont
     */
    function font($file, $size, Color $color);
}
