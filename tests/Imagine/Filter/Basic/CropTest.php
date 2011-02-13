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

use Imagine\Point;

class CropTest extends BasicFilterTestCase
{
    /**
     * @dataProvider getDataSet
     */
    public function testShouldApplyCropAndReturnResult(Point $start, $width, $height)
    {
        $image = $this->getImage();

        $command = new Crop($start, $width, $height);

        $image->expects($this->once())
            ->method('crop')
            ->with($start, $width, $height)
            ->will($this->returnValue($image));

        $this->assertSame($image, $command->apply($image));
    }

    public function getDataSet()
    {
        return array(
            array(new Point(0, 0), 40, 50),
            array(new Point(0, 15), 50, 32)
        );
    }
}
