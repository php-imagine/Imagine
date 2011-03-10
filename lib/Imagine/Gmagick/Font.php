<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Gmagick;

use Imagine\Image\AbstractFont;
use Imagine\Image\Box;
use Imagine\Image\Color;

final class Font extends AbstractFont
{
    /**
     * @var Gmagick
     */
    private $gmagick;

    /**
     * @param Gmagick             $gmagick
     * @param string              $file
     * @param integer             $size
     * @param Imagine\Image\Color $color
     */
    public function __construct(\Gmagick $gmagick, $file, $size, Color $color)
    {
        $this->gmagick = $gmagick;

        parent::__construct($file, $size, $color);
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Image\AbstractFont::box()
     */
    public function box($string, $angle = 0)
    {
        $text  = new \GmagickDraw();

        $text->setfont($this->file);
        $text->setfontsize($this->size);
        $text->setfontstyle(\Gmagick::STYLE_OBLIQUE);

        $info = $this->gmagick->queryfontmetrics($text, $string);

        $box = new Box($info['textWidth'], $info['textHeight']);

        return $box;
    }
}
