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

use Imagine\Image\Box;
use Imagine\Image\BoxInterface;
use Imagine\Image\Point;
use Imagine\Image\PointInterface;
use Imagine\Image\Color;
use Imagine\Filter\FilterTestCase;

class CanvasTest extends FilterTestCase
{
    /**
     * @covers Imagine\Filter\Advanced\Canvas::apply
     *
     * @dataProvider getDataSet
     *
     * @param BoxInterface $size
     * @param PointInterface $placement
     * @param Color $background
     */
    public function testShouldCanvasImageAndReturnResult(BoxInterface $size, PointInterface $placement = null, Color $background = null)
    {
        $image = $this->getImage();

        $image->expects($this->once())
            ->method('canvas')
            ->with($size, $placement, $background)
            ->will($this->returnValue($image));

        $command = new Canvas($size, $placement, $background);

        $this->assertSame($image, $command->apply($image));
    }

    /**
     * Data provider for testShouldCanvasImageAndReturnResult
     *
     * @return array
     */
    public function getDataSet()
    {
        return array(
            array(new Box(50, 15), new Point(10, 10), new Color('fff')),
            array(new Box(300, 25), new Point(15, 15)),
            array(new Box(123, 23)),
        );
    }
}
