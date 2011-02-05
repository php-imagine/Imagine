<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Filter\Basic;

use Imagine\Color;
use Imagine\ImageInterface;
use Imagine\Filter\FilterInterface;

class Thumbnail implements FilterInterface
{
    private $width;
    private $height;
    private $mode;
    private $background;

    /**
     * Constructs the Thumbnail filter with given width, height, mode and
     * background color
     *
     * @param integer $width
     * @param integer $height
     * @param string  $mode
     * @param Color   $background
     */
        public function __construct($width, $height, $mode = ImageInterface::THUMBNAIL_INSET, Color $background = null)
    {
        $this->width      = $width;
        $this->height     = $height;
        $this->mode       = $mode;
        $this->background = $background;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Filter.FilterInterface::apply()
     */
    public function apply(ImageInterface $image)
    {
        return $image->thumbnail($this->width, $this->height, $this->mode, $this->background);
    }
}
