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
use Imagine\Image\ImageInterface;
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
        $draw
            ->line(
                new Point(0, 0),
                new Point($width - 1, 0),
                $this->color,
                $this->height
            )
            ->line(
                new Point($width - 1, $height - 1),
                new Point(0, $height - 1),
                $this->color,
                $this->height
            )
        ;

        // Draw sides
        $draw
            ->line(
                new Point(0, 0),
                new Point(0, $height - 1),
                $this->color,
                $this->width
            )
            ->line(
                new Point($width - 1, 0),
                new Point($width - 1, $height - 1),
                $this->color,
                $this->width
            )
        ;

        return $image;
    }
}
