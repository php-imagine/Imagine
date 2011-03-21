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

abstract class AbstractFont implements FontInterface
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
     * @param string              $file
     * @param integer             $size
     * @param Imagine\Image\Color $color
     */
    public function __construct($file, $size, Color $color)
    {
        $this->file  = $file;
        $this->size  = $size;
        $this->color = $color;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\FontInterface::getFile()
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\FontInterface::getSize()
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\FontInterface::getColor()
     */
    public function getColor()
    {
        return $this->color;
    }
}
