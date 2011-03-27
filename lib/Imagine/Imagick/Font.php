<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Imagick;

use Imagine\Image\AbstractFont;
use Imagine\Image\Box;
use Imagine\Image\Color;

final class Font extends AbstractFont
{
    /**
     * @var Imagick
     */
    private $imagick;

    /**
     * @param Imagick             $imagick
     * @param string              $file
     * @param integer             $size
     * @param Imagine\Image\Color $color
     */
    public function __construct(\Imagick $imagick, $file, $size, Color $color)
    {
        $this->imagick = $imagick;

        parent::__construct($file, $size, $color);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\FontInterface::box()
     */
    public function box($string, $angle = 0)
    {
        $text  = new \ImagickDraw();

        $text->setFont($this->file);
        $text->setFontSize($this->size);

        $info = $this->imagick->queryFontMetrics($text, $string);

        $box = new Box($info['textWidth'], $info['textHeight']);

        return $box;
    }
}
