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

use Imagine\Cartesian\SizeInterface;
use Imagine\Exception\RuntimeException;
use Imagine\Exception\InvalidArgumentException;

interface ImagineInterface
{
    /**
     * Creates a new empty image with an optional background color
     *
     * @param SizeInterface $size
     * @param Color   $color
     *
     * @throws InvalidArgumentException
     *
     * @return ImageInterface
     */
    function create(SizeInterface $size, Color $color = null);

    /**
     * Opens an existing image from $path
     *
     * @param string $path
     *
     * @throws RuntimeException
     *
     * @return ImageInterface
     */
    function open($path);
}
