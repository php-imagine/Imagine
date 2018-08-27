<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Test\Filter\Advanced;

use Imagine\Filter\Advanced\BlackWhite;
use Imagine\Image\Palette\RGB;
use Imagine\Image\Point;
use Imagine\Test\Filter\FilterTestCase;

class BlackWhiteTest extends FilterTestCase
{
    /**
     * @dataProvider getData
     *
     * @param mixed $border
     * @param mixed $currentColor
     * @param mixed $expectedColor
     */
    public function testCallback($border, $currentColor, $expectedColor)
    {
        $rgb = new RGB();
        $image = $this->getImage();

        $size = $this->getMockBuilder('Imagine\\Image\\BoxInterface')->getMock();
        $size
            ->expects($this->any())
            ->method('getWidth')
            ->will($this->returnValue(1))
        ;

        $size
            ->expects($this->any())
            ->method('getHeight')
            ->will($this->returnValue(1))
        ;

        $image
            ->expects($this->any())
            ->method('getSize')
            ->will($this->returnValue($size))
        ;

        $currentColor = $rgb->color($currentColor);

        $image
            ->expects($this->any())
            ->method('getColorAt')
            ->will($this->returnValue($currentColor))
        ;

        $drawer = $this->getDrawer();
        $drawer
            ->expects($this->at(1))
            ->method('dot')
            ->with(new Point(0, 0), $rgb->color($expectedColor))
        ;

        $image
            ->expects($this->any())
            ->method('draw')
            ->will($this->returnValue($drawer))
        ;

        $blackWhiteFilter = new BlackWhite($border);
        $blackWhiteFilter->apply($image);
    }

    public function getData()
    {
        return array(
            array(150, array(150, 150, 150), array(0, 0, 0)),
            array(151, array(150, 150, 150), array(255, 255, 255)),
            array(0, array(150, 150, 150), array(0, 0, 0)),
            array(255, array(150, 150, 150), array(255, 255, 255)),
        );
    }
}
