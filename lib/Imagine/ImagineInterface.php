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
     * @param Imagine\Color                    $color
     *
     * @throws Imagine\Exception\InvalidArgumentException
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
     * Opens a font for usage with the drawing api
     *
     * @param string        $path
     * @param integer       $size
     * @param Imagine\Color $color
     *
     * @return Imagine\Font\FontInterface
     */
    function font($path, $size, Color $color);
}
