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

use Imagine\Image\Box;
use Imagine\Image\ManipulatorInterface;
use Imagine\Filter\FilterTestCase;

class ThumbnailTest extends FilterTestCase
{
    public function testShouldMakeAThumbnail()
    {
        $image     = $this->getImage();
        $thumbnail = $this->getImage();
        $size      = new Box(50, 50);
        $filter    = new Thumbnail($size);

        $image->expects($this->once())
            ->method('thumbnail')
            ->with($size, ManipulatorInterface::THUMBNAIL_INSET)
            ->will($this->returnValue($thumbnail));

        $this->assertSame($thumbnail, $filter->apply($image));
    }
}
