<?php

namespace Imagine\Filter\Basic;

use Imagine\ImageInterface;

class ThumbnailTest extends BasicFilterTestCase
{
    public function testShouldFlipImage()
    {
        $width     = 50;
        $height    = 50;
        $image     = $this->getImage();
        $thumbnail = $this->getImage();
        $filter    = new Thumbnail($width, $height);

        $image->expects($this->once())
            ->method('thumbnail')
            ->with($width, $height, ImageInterface::THUMBNAIL_INSET)
            ->will($this->returnValue($thumbnail));

        $this->assertSame($thumbnail, $filter->apply($image));
    }
}
