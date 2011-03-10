<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Image;

abstract class AbstractFont
{
    /**
     * @var string
     */
    protected $file;

    /**
     * @var integer
     */
    protected $size;

    /**
     * @var Imagine\Image\Color
     */
    protected $color;

    /**
     * Constructs a font with specified $file, $size and $color
     *
     * The font size is to be specified in points (e.g. 10pt means 10)
     *
     * @param string        $file
     * @param integer       $size
     * @param Imagine\Image\Color $color
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
     * @return Imagine\Image\Color
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Gets BoxInterface of font size on the image based on string and angle
     *
     * @param string  $string
     * @param integer $angle
     *
     * @return Imagine\Image\BoxInterface
     */
    abstract public function box($string, $angle = 0);
}
