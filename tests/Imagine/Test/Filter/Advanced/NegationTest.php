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

use Imagine\Filter\Advanced\Negation;
use Imagine\Image\Palette\RGB;
use Imagine\Image\Point;
use Imagine\Test\Filter\FilterTestCase;


/**
 * @author Tony Lemke <naji@mail.upb.de>
 */
class NegationTest extends FilterTestCase
{
    /**
     * @dataProvider getData
     */
    public function testCallback($currentColor, $expectedColor)
    {
        $rgb = new RGB();
        $image = $this->getImage();

        $size = $this->getMock('Imagine\\Image\\BoxInterface');
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
            ->expects($this->once())
            ->method('dot')
            ->with(new Point(0, 0), $rgb->color($expectedColor))
        ;

        $image
            ->expects($this->any())
            ->method('draw')
            ->will($this->returnValue($drawer))
        ;

        $blackWhiteFilter = new Negation();
        $blackWhiteFilter->apply($image);
    }

    public function getData()
    {
        return array(
            array(array(150, 124, 83), array(105, 131, 172)),
            array(array(132, 169, 31), array(123, 86, 224)),
            array(array(234, 122, 11), array(21, 133, 244))
        );
    }
}
