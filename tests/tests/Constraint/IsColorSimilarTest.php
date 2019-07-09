<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Test\Constraint;

use Imagine\Image\Palette\CMYK;
use Imagine\Image\Palette\Color\ColorInterface;
use Imagine\Image\Palette\Grayscale;
use Imagine\Image\Palette\RGB;
use Imagine\Test\ImagineTestCase;

class IsColorSimilarTest extends ImagineTestCase
{
    public function isColorSimilarShouldFailProvider()
    {
        $rgb = new RGB();
        $grayscale = new Grayscale();
        $cmyk = new CMYK();

        return array(
            array($rgb->color('#000000'), $rgb->color('#ffffff'), 0),
            array($grayscale->color('#000000'), $grayscale->color('#ffffff'), 0),
            array($cmyk->color('#000000'), $cmyk->color('#ffffff'), 0),
            array($rgb->color('#000000'), $rgb->color('#000001'), sqrt(1) - 0.00001),
            array($rgb->color('#000000'), $rgb->color('#000101'), sqrt(2) - 0.00001),
            array($rgb->color('#000000'), $rgb->color('#010101'), sqrt(3) - 0.00001),
            array($grayscale->color('#000000'), $grayscale->color('#010101'), 1 - 0.00001),
            array($rgb->color('#000000', 50), $rgb->color('#000000', 51), 2.55 - 0.00001),
        );
    }

    /**
     * @dataProvider isColorSimilarShouldFailProvider
     *
     * @param \Imagine\Image\Palette\Color\ColorInterface $color1
     * @param \Imagine\Image\Palette\Color\ColorInterface $color2
     * @param float $maxDistance
     */
    public function testIsColorSimilarShouldFail(ColorInterface $color1, ColorInterface $color2, $maxDistance)
    {
        $this->isGoingToThrowException('PHPUnit\Framework\ExpectationFailedException');
        $this->assertColorSimilar($color1, $color2, '', $maxDistance);
    }

    public function isColorSimilarShouldSucceedProvider()
    {
        $rgb = new RGB();
        $grayscale = new Grayscale();
        $cmyk = new CMYK();

        return array(
            array($rgb->color('#000000'), $rgb->color('#000000'), 0),
            array($grayscale->color('#ffffff'), $grayscale->color('#ffffff'), 0),
            array($cmyk->color('#777777'), $cmyk->color('#777777'), 0),
            array($rgb->color('#000000'), $rgb->color('#000001'), sqrt(1) + 0.00001),
            array($rgb->color('#000000'), $rgb->color('#000101'), sqrt(2) + 0.00001),
            array($rgb->color('#000000'), $rgb->color('#010101'), sqrt(3) + 0.00001),
            array($grayscale->color('#000000'), $grayscale->color('#010101'), 1 + 0.00001),
            array($rgb->color('#000000', 50), $rgb->color('#000000', 51), 2.55 + 0.00001),
        );
    }

    /**
     * @dataProvider isColorSimilarShouldSucceedProvider
     *
     * @param \Imagine\Image\Palette\Color\ColorInterface $color1
     * @param \Imagine\Image\Palette\Color\ColorInterface $color2
     * @param float $maxDistance
     */
    public function testIsColorSimilarShouldSucceed(ColorInterface $color1, ColorInterface $color2, $maxDistance)
    {
        $this->assertColorSimilar($color1, $color2, '', $maxDistance);
    }
}
