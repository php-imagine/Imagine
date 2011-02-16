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

use Imagine\Cartesian\Coordinate;
use Imagine\Cartesian\CoordinateInterface;
use Imagine\Cartesian\Size;
use Imagine\Cartesian\SizeInterface;

class CropTest extends BasicFilterTestCase
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
