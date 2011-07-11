<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Filter\Advanced;

use Imagine\Filter\FilterInterface;
use Imagine\ImageInterface;
use Imagine\Image\Color;
use Imagine\Image\Point;

class Border implements FilterInterface
{
    /**
     * @var Imagine\Image\Color
     */
    private $color;

    /**
     * @var int
     */
    private $width;

    /**
     * @var int
     */
    private $height;

    /**
     * Constructs Border filter with given color, width and height
     *
     * @param Imagine\Image\Color $color
     * @param int $width Width of the border on the left and right sides of the image
     * @param int $height Height of the border on the top and bottom sides of the image
     */
    public function __construct(Color $color, $width = 1, $height = 1)
    {
        $this->color = $color;
        $this->width = $width;
        $this->height = $height;
    }

    /**
     * (non-PHPdoc)
     * @see Imagine\Filter\FilterInterface::apply()
     */
    public function apply(ImageInterface $image)
    {
        $size = $image->getSize();
        $width = $size->getWidth();
        $height = $size->getHeight();

        $draw = $image->draw();

        // Draw top and bottom lines
        for ($i = 0; $i < $this->height; $i++) {
            $draw->line(new Point(0, $i), new Point($width - 1, $i), $this->color)
                 ->line(new Point($width - 1, $height - ($i + 1)), new Point(0, $height - ($i + 1)), $this->color);
        }

        // Draw sides
        for ($i = 0; $i < $this->width; $i++) {
            $draw->line(new Point($i, 0), new Point($i, $height - 1), $this->color)
                 ->line(new Point($width - ($i + 1), 0), new Point($width - ($i + 1), $height - 1), $this->color);
        }

        return $image;
    }
}
