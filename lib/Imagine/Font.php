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

final class Font
{
    private $file;
    private $size;
    private $color;

    public function __construct($file, $size, Color $color)
    {
        $this->file  = $file;
        $this->size  = $size;
        $this->color = $color;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function getColor()
    {
        return $this->color;
    }
}
