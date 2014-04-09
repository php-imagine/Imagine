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
use Imagine\Image\Palette\Color\ColorInterface;
use Imagine\Image\BoxFactoryInterface;

/**
 * Font implementation using the Imagick PHP extension
 */
final class Font extends AbstractFont
{
    /**
     * @var \Imagick
     */
    private $imagick;

    /**
     * @param \Imagick            $imagick
     * @param string              $file
     * @param integer             $size
     * @param ColorInterface      $color
     * @param BoxFactoryInterface $boxFactory
     */
    public function __construct(\Imagick $imagick, $file, $size, ColorInterface $color, 
            BoxFactoryInterface $boxFactory = null)
    {
        $this->imagick = $imagick;

        parent::__construct($file, $size, $color, $boxFactory);
    }

    /**
     * {@inheritdoc}
     */
    public function box($string, $angle = 0)
    {
        $text = new \ImagickDraw();

        $text->setFont($this->file);

        /**
         * @see http://www.php.net/manual/en/imagick.queryfontmetrics.php#101027
         *
         * ensure font resolution is the same as GD's hard-coded 96
         */
        if (version_compare(phpversion("imagick"), "3.0.2", ">=")) {
            $text->setResolution(96, 96);
            $text->setFontSize($this->size);
        } else {
            $text->setFontSize((int) ($this->size * (96 / 72)));
        }

        $info = $this->imagick->queryFontMetrics($text, $string);

        $box = $this->getBoxFactory()->createBox($info['textWidth'], $info['textHeight']);

        return $box;
    }
}
