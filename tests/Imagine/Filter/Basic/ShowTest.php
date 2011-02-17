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

use Imagine\Filter\FilterTestCase;

class ShowTest extends FilterTestCase
{
    public function testShouldShowImageAndReturnResult()
    {
        $image   = $this->getImage();
        $format  = 'jpg';
        $command = new Show($format);

        $image->expects($this->once())
            ->method('show')
            ->with($format)
            ->will($this->returnValue($image));

        $this->assertSame($image, $command->apply($image));
    }
}
