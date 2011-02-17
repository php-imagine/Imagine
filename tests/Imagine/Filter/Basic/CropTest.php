<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Filter\Basic;

use Imagine\Coordinate\Coordinate;
use Imagine\Coordinate\CoordinateInterface;
use Imagine\Coordinate\Size;
use Imagine\Coordinate\SizeInterface;
use Imagine\Filter\FilterTestCase;

class CropTest extends FilterTestCase
{
    /**
     * @covers Imagine\Filter\Basic\Crop::apply
     *
     * @dataProvider getDataSet
     *
     * @param CoordinateInterface $start
     * @param SizeInterface       $size
     */
    public function testShouldApplyCropAndReturnResult(CoordinateInterface $start, SizeInterface $size)
    {
        $image = $this->getImage();

        $command = new Crop($start, $size);

        $image->expects($this->once())
            ->method('crop')
            ->with($start, $size)
            ->will($this->returnValue($image));

        $this->assertSame($image, $command->apply($image));
    }

    /**
     * Provides coordinates and sizes for testShouldApplyCropAndReturnResult
     *
     * @return array
     */
    public function getDataSet()
    {
        return array(
            array(new Coordinate(0, 0), new Size(40, 50)),
            array(new Coordinate(0, 15), new Size(50, 32))
        );
    }
}
