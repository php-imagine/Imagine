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

use Imagine\Exception\RuntimeException;
use Imagine\Exception\InvalidArgumentException;

interface ImageFactoryInterface
{
    /**
     * Creates a new empty image
     *
     * @param integer $width
     * @param integer $height
     *
     * @throws InvalidArgumentException
     *
     * @return ImageInterface
     */
    function create($width, $height);

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
