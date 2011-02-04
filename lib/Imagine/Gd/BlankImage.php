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

use Imagine\Color;
use Imagine\Exception\RuntimeException;

final class BlankImage extends Image
{
    /**
     * Constructs a blank image in memory for given dimensions
     * If optional Color parameter is given, paints image into it
     * Throws exception if image creation fails
     *
     * @param integer $width
     * @param integer $height
     * @param Color   $color
     *
     * @throws RuntimeException
     */
    public function __construct($width, $height, Color $color = null)
    {
        $this->resource = imagecreatetruecolor($width, $height);

        imagealphablending($this->resource, false);
        imagesavealpha($this->resource, true);

        if (false === $this->resource) {
            throw new RuntimeException('Create operation failed');
        }

        if (null !== $color) {
            imagefilledrectangle($this->resource, 0, 0, $width, $height, $this->getColor($color));
        }

        $this->width    = $width;
        $this->height   = $height;
    }
}
