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
use Imagine\Image\Palette\PaletteInterface;
use Imagine\Image\Point;
use Imagine\Utils\Matrix;

/**
 * The Neighborhood filter takes a matrix and calculates the color current pixel based on its neighborhood. For example:
 *
 *           a, b, c
 * Matrix =  d, e, f
 *           g, h, i
 *
 * and color{i, j} the color of the pixel at position (i, j). It calculates the color of pixel (x, y) like that:
 *
 * color (x, y) =   a * color(x-1, y-1) + b * color(x, y-1) + c * color(x+1, y-1)
 *                + d * color(x-1, y)   + e * color(x, y)   + f * color(x+1, y)
 *                + g * color(x-1, y+1) + h * color(x, y+1) + i * color(x+1, y+1)
 */
class Neighborhood implements FilterInterface
{
    /**
     * @var Matrix
     */
    protected $matrix;

    public function __construct(Matrix $matrix)
    {
        $this->matrix = $matrix;
    }

    /**
     * Applies scheduled transformation to ImageInterface instance
     * Returns processed ImageInterface instance
     *
     * @param ImageInterface $image
     *
     * @return ImageInterface
     */
    public function apply(ImageInterface $image)
    {
        // We reduce the usage of methods on the image to dramatically increase the performance of this algorithm.
        // Really... We need that performance...
        // Therefore we first build a matrix, that holds the colors of the image.
        $width  = $image->getSize()->getWidth();
        $height = $image->getSize()->getHeight();
        $byteData = new Matrix($width, $height);

        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $byteData->setElementAt($x, $y, $image->getColorAt(new Point($x, $y)));
            }
        }

        $dWidth  = (int) (($this->matrix->getWidth()  - 1) / 2);
        $dHeight = (int) (($this->matrix->getHeight() - 1) / 2);

        // foreach point, which has a big enough neighborhood
        for ($y = $dHeight; $y < $height - $dHeight; $y++) {
            for ($x = $dWidth; $x < $width - $dWidth; $x++) {
                $currentColor = array_fill(0, count($image->palette()->pixelDefinition()), 0);

                // calculate the new color based on the neighborhood
                for ($boxY = $y - $dHeight, $matrixY = 0; $boxY <= $y + $dHeight; $boxY++, $matrixY++) {
                    for ($boxX = $x - $dWidth, $matrixX = 0; $boxX <= $x + $dWidth; $boxX++, $matrixX++) {
                        foreach ($image->palette()->pixelDefinition() as $index => $stream) {
                            $currentColor[$index] +=
                                $byteData->getElementAt($boxX, $boxY)->getValue($stream) *
                                $this->matrix->getElementAt($matrixX, $matrixY)
                            ;
                        }
                    }
                }

                $image->draw()->dot(new Point($x, $y), $image->palette()->color($currentColor));
            }
        }

        return $image;
    }
}