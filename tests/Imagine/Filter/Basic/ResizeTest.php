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

use Imagine\Cartesian\Size;
use Imagine\Cartesian\SizeInterface;
use Imagine\Filter\FilterTestCase;

class ResizeTest extends FilterTestCase
{
    /**
     * @covers Imagine\Filter\Basic\Resize::apply
     *
     * @dataProvider getDataSet
     *
     * @param SizeInterface $size
     */
    public function testShouldResizeImageAndReturnResult(SizeInterface $size)
    {
        $image = $this->getImage();

        $image->expects($this->once())
            ->method('resize')
            ->with($size)
            ->will($this->returnValue($image));

        $command = new Resize($size);

        $this->assertSame($image, $command->apply($image));
    }

    /**
     * Data provider for testShouldResizeImageAndReturnResult
     *
     * @return array
     */
    public function getDataSet()
    {
        return array(
            array(new Size(50, 15)),
            array(new Size(300, 25)),
            array(new Size(123, 23)),
            array(new Size(45, 23))
        );
    }
}
