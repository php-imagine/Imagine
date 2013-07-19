<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Test\Image\Palette\Color;

use Imagine\Image\Palette\Color\Gray;
use Imagine\Image\Palette\Color\ColorInterface;
use Imagine\Image\Palette\Grayscale;

class GrayTest extends AbstractColorTest
{
    public function provideOpaqueColors()
    {
        return array(
            array(new Gray(new Grayscale(), array(12), 0)),
            array(new Gray(new Grayscale(), array(0), 0)),
            array(new Gray(new Grayscale(), array(255), 0)),
        );
    }
    public function provideNotOpaqueColors()
    {
        return array(
            array($this->getColor()),
            array(new Gray(new Grayscale(), array(12), 23)),
            array(new Gray(new Grayscale(), array(0), 45)),
            array(new Gray(new Grayscale(), array(255), 100)),
        );
    }

    public function provideGrayscaleData()
    {
        return array(
            array('#0c0c0c', $this->getColor()),
        );
    }

    public function provideColorAndAlphaTuples()
    {
        return array(
            array(14, $this->getColor())
        );
    }

    protected function getColor()
    {
        return new Gray(new Grayscale(), array(12), 14);
    }

    public function provideColorAndValueComponents()
    {
        return array(
            array(array(
                ColorInterface::COLOR_GRAY => 12,
            ), $this->getColor()),
        );
    }
}
