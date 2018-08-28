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

use Imagine\Filter\Advanced\OnPixelBased;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\Point;
use Imagine\Test\Filter\FilterTestCase;

class OnPixelBasedTest extends FilterTestCase
{
    /**
     * @dataProvider getDataSet
     *
     * @param Box $size
     * @param mixed $expectedVisitedCoordinates
     */
    public function testEachPixelIsVisited(Box $size, $expectedVisitedCoordinates)
    {
        $image = $this->getImage();
        $imageWidth = $size->getWidth();
        $imageHeight = $size->getHeight();

        $size = $this->getMockBuilder('Imagine\\Image\\BoxInterface')->getMock();
        $size
            ->expects($this->exactly(1))
            ->method('getHeight')
            ->will($this->returnValue($imageHeight))
        ;

        $size
            ->expects($this->exactly(1))
            ->method('getWidth')
            ->will($this->returnValue($imageWidth))
        ;

        $image
            ->expects($this->exactly(1))
            ->method('getSize')
            ->will($this->returnValue($size))
        ;

        $visitedCoordinates = array();
        $filter = new OnPixelBased(function (ImageInterface $image, Point $point) use (&$visitedCoordinates) {
            $visitedCoordinates[] = $point->getX() . ',' . $point->getY();
        });

        $this->assertSame($image, $filter->apply($image));
        $this->assertEquals($expectedVisitedCoordinates, $visitedCoordinates);
    }

    /**
     * Data provider for testEachPixelIsVisited.
     *
     * @return array
     */
    public function getDataSet()
    {
        return array(
            array(new Box(1, 3), array('0,0', '0,1', '0,2')),
            array(new Box(3, 3), array('0,0', '1,0', '2,0', '0,1', '1,1', '2,1', '0,2', '1,2', '2,2')),
            array(new Box(3, 2), array('0,0', '1,0', '2,0', '0,1', '1,1', '2,1')),
        );
    }
}
