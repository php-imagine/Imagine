<?php

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
