<?php

namespace Imagine\Filter\Basic;

class FlipHorizontallyTest extends BasicFilterTestCase
{
    public function testShouldFlipImage()
    {
        $image  = $this->getImage();
        $filter = new FlipHorizontally();

        $image->expects($this->once())
            ->method('flipHorizontally')
            ->will($this->returnValue($image));

        $this->assertSame($image, $filter->apply($image));
    }
}
