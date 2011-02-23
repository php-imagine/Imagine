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

use Imagine\BoxInterface;
use Imagine\Exception\RuntimeException;
use Imagine\Exception\InvalidArgumentException;

interface ImagineInterface
{
    /**
     * Creates a new empty image with an optional background color
     *
     * @param Imagine\BoxInterface $size
     * @param Imagine\Color        $color
     *
     * @throws Imagine\Exception\InvalidArgumentException
     * @throws Imagine\Exception\RuntimeException
     *
     * @return Imagine\ImageInterface
     */
    function create(BoxInterface $size, Color $color = null);

    /**
     * Opens an existing image from $path
     *
     * @param string $path
     *
     * @throws Imagine\Exception\RuntimeException
     *
     * @return Imagine\ImageInterface
     */
    function open($path);

    /**
     * Loads an image from a binary $string
     *
     * @param string $string
     *
     * @throws Imagine\Exception\RuntimeException
     *
     * @return Imagine\ImageInterface
     */
    function load($string);
}
