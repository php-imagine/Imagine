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

final class BlankImage extends Image
{
    public function __construct($width, $height)
    {
        $this->width    = $width;
        $this->height   = $height;
        if (!($this->resource = imagecreatetruecolor($width, $height))) {
            throw new RuntimeException('Create operation failed');
        }
    }
}
