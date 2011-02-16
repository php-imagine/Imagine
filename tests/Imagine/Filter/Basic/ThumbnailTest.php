<?php

namespace Imagine\Filter\Basic;

use Imagine\Cartesian\Size;
use Imagine\ImageInterface;

class ThumbnailTest extends BasicFilterTestCase
{
    /**
     * @covers Imagine\Filter\Basic::apply
     */
    public function testShouldMakeAThumbnail()
    {
        $image     = $this->getImage();
        $thumbnail = $this->getImage();
        $size      = new Size(50, 50);
        $filter    = new Thumbnail($size);

        $image->expects($this->once())
            ->method('thumbnail')
            ->with($size, ImageInterface::THUMBNAIL_INSET)
            ->will($this->returnValue($thumbnail));

        $this->assertSame($thumbnail, $filter->apply($image));
    }
}
