<?php

namespace Imagine\Filter\Basic;

class FlipVerticallyTest extends BasicFilterTestCase
{
    public function testShouldFlipImage()
    {
        $image  = $this->getImage();
        $filter = new FlipVertically();

        $image->expects($this->once())
            ->method('flipVertically')
            ->will($this->returnValue($image));

        $this->assertSame($image, $filter->apply($image));
    }
}
