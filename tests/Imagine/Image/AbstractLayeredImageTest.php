<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Image;

abstract class AbstractLayeredImageTest extends \PHPUnit_Framework_TestCase
{
    protected function testGetColorAtWorksWithFirstLayer()
    {
        $image = $this->getMultiLayeredImage();

        $point = new Point(130, 77);
        $color = $image->getColorAt($point);
        $this->assertEquals("#78874e", (string) $color);
    }

    protected function getMultiLayeredImage()
    {
        return $this->getImagine()->open('tests/Imagine/Fixtures/cat.gif');
    }

    abstract protected function getImagine();
    abstract protected function supportDelays();
}
