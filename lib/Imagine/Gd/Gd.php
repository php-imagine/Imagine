<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Gd;

use Imagine\Exception\RuntimeException;

/**
 * Gd resource wrapper for proper garbage collection.
 */
final class Gd
{
    /**
     * @var resource
     */
    public $resource;

    /**
     * @param resource $resource
     */
    public function __construct($resource)
    {
        if (!is_resource($resource) || 'gd' !== get_resource_type($resource)) {
            throw new RuntimeException("Given resource not a valid GD resource");
        }

        $this->resource = $resource;
    }

    /**
     * Makes sure the image resource is destroyed
     */
    public function __destruct()
    {
        if (is_resource($this->resource) && 'gd' === get_resource_type($this->resource)) {
            imagedestroy($this->resource);
        }
    }
}