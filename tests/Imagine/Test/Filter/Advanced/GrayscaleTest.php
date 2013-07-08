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

use Imagine\Filter\Advanced\Grayscale;
use Imagine\Image\Box;
use Imagine\Image\BoxInterface;
use Imagine\Image\Color;
use Imagine\Image\Point;
use Imagine\Test\Filter\FilterTestCase;

class GrayscaleTest extends FilterTestCase
{
    /**
     * @covers \Imagine\Filter\Advanced\Grayscale::apply
     *
     * @dataProvider getDataSet
     *
     * @param \Imagine\Image\BoxInterface $size
     * @param \Imagine\Image\Color        $color
     * @param \Imagine\Image\Color        $filteredColor
     */
    public function testGrayscaling(BoxInterface $size, Color $color, Color $filteredColor)
    {
        $image       = $this->getImage();
        $imageWidth  = $size->getWidth();
        $imageHeight = $size->getHeight();

        $size = $this->getMock('Imagine\\Image\\BoxInterface');
        $size->expects($this->exactly($imageWidth+1))
             ->method('getWidth')
             ->will($this->returnValue($imageWidth));

        $size->expects($this->exactly($imageWidth * ($imageHeight+1)))
             ->method('getHeight')
             ->will($this->returnValue($imageHeight));

        $image->expects($this->any())
            ->method('getSize')
            ->will($this->returnValue($size));

        $image->expects($this->exactly($imageWidth*$imageHeight))
            ->method('getColorAt')
            ->will($this->returnValue($color));

        $draw = $this->getDrawer();
        $draw->expects($this->exactly($imageWidth*$imageHeight))
            ->method('dot')
            ->with($this->isInstanceOf('Imagine\\Image\\Point'), $this->equalTo($filteredColor));

        $image->expects($this->exactly($imageWidth*$imageHeight))
              ->method('draw')
              ->will($this->returnValue($draw));

        $filter = new Grayscale();
        $this->assertSame($image, $filter->apply($image));
    }

    /**
     * Data provider for testShouldCanvasImageAndReturnResult
     *
     * @return array
     */
    public function getDataSet()
    {
        return array(
            array(new Box(20, 10), new Color('#D02090'), new Color('#808080')),
            array(new Box(10, 15), new Color('#778899'), new Color('#888888')),
            array(new Box(12, 23), new Color('#00FA9A'), new Color('#878787')),
        );
    }
}
