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
use Imagine\Image\Palette\Color\ColorInterface;
use Imagine\Image\Palette\RGB;
use Imagine\Image\Point;

/**
 * This filter negates every color of every pixel of an image.
 */
class Negation extends OnPixelBased implements FilterInterface
{
    public function __construct()
    {
        $rgb = new RGB();
        parent::__construct(function(ImageInterface $image, Point $point) use ($rgb)
        {
            $color = $image->getColorAt($point);
            $image->draw()->dot($point, $rgb->color(array(
                255 - $color->getValue(ColorInterface::COLOR_RED),
                255 - $color->getValue(ColorInterface::COLOR_GREEN),
                255 - $color->getValue(ColorInterface::COLOR_BLUE)
            )));
        });
    }
}