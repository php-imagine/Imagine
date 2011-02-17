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
use Imagine\Filter\FilterTestCase;

class PasteTest extends FilterTestCase
{
    public function testShouldFlipImage()
    {
        $start   = new Coordinate(0, 0);
        $image   = $this->getImage();
        $toPaste = $this->getImage();
        $filter  = new Paste($toPaste, $start);

        $image->expects($this->once())
            ->method('paste')
            ->with($toPaste, $start)
            ->will($this->returnValue($image));

        $this->assertSame($image, $filter->apply($image));
    }
}
