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
    /**
     * @var string
     */
    private $file;

    /**
     * @var integer
     */
    private $size;

    /**
     * @var Imagine\Color
     */
    private $color;

    /**
     * Constructs a font with specified $file, $size and $color
     *
     * The font size is to be specified in points (e.g. 10pt means 10)
     *
     * @param string        $file
     * @param integer       $size
     * @param Imagine\Color $color
     */
    public function __construct($file, $size, Color $color)
    {
        $this->file  = $file;
        $this->size  = $size;
        $this->color = $color;
    }

    /**
     * Gets the fontfile for current font
     *
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Gets font's integer point size
     *
     * @return integer
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Gets font's color
     *
     * @return Imagine\Color
     */
    public function getColor()
    {
        return $this->color;
    }
}
