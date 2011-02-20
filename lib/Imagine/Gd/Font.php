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

use Imagine\Box;
use Imagine\Color;
use Imagine\Exception\RuntimeException;
use Imagine\Font\FontInterface;

final class Font implements FontInterface
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var integer
     */
    private $size;

    /**
     * @var Imagine\Color
     */
    private $color;

    /**
     * Constructs Font with given font path, font size in pts, angle and
     * Imagine\Color instance
     *
     * @param string        $path
     * @param integer       $size
     * @param Imagine\Color $color
     */
    public function __construct($path, $size, Color $color)
    {
        $this->path  = $path;
        $this->size  = $size;
        $this->color = $color;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Font.FontInterface::getSize()
     */
    public function getSize($text)
    {
        $info = imageftbbox($this->size, 0, $this->path, $text);

        list($x1, $y1, $x2, $y2, $x3, $y3, $x4, $y4) = $info;

        return new Box(
            round(abs(max($x1, $x2, $x3, $x4) - min($x1, $x2, $x3, $x4)) * 1.1),
            round(abs(max($y1, $y2, $y3, $y4) - min($y1, $y2, $y3, $y4)) * 1.1)
        );
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Font.FontInterface::mask()
     */
    public function mask($text)
    {
        $size   = $this->getSize($text);
        $width  = $size->getWidth();
        $height = $size->getHeight();
        $image = imagecreatetruecolor($width, $height);

        if (false === $image ||
            false === imagealphablending($image, false) ||
            false === imagesavealpha($image, true)) {
            throw new RuntimeException('Font mask operation failed');
        }

        $background = imagecolorallocatealpha($image, 255, 255, 255, 127);

        if (false === $background ||
            false === imagefilledrectangle(
                $image, 0, 0, $width, $height, $background
            ) ||
            false === imagealphablending($image, true)) {
            throw new RuntimeException('Font mask operation failed');
        }

        if (false === imagefttext(
                $image,
                $this->size,
                0,
                0,
                $height,
                $this->getColor($image),
                $this->path,
                $text
            )) {
            throw new RuntimeException('Font mask operation failed');
        }

        if (false === imagealphablending($image, false)) {
            throw new RuntimeException('Font mask operation failed');
        }

        return new Image($image);
    }

    /**
     * Internal
     *
     * Generates a GD color from a given gd resource
     *
     * @param resource $resource
     *
     * @throws RuntimeException
     *
     * @return resource
     */
    private function getColor($resource)
    {
        $color = imagecolorallocatealpha(
            $resource, $this->color->getRed(), $this->color->getGreen(),
            $this->color->getBlue(),
            round(127 * $this->color->getAlpha() / 100)
        );

        if (false === $color) {
            throw new RuntimeException(sprintf(
                'Unable to allocate color "RGB(%s, %s, %s)" with '.
                'transparency of %d percent', $this->color->getRed(),
                $this->color->getGreen(), $this->color->getBlue(),
                $this->color->getAlpha()
            ));
        }

        return $color;
    }
}
