<?php

namespace Imagine\Filter\Basic;

class CopyTest extends BasicFilterTestCase
{
    public function testShouldCopyAndReturnResultingImage()
    {
        $command = new Copy();
        $image   = $this->getImage();
        $clone   = $this->getImage();

        $image->expects($this->once())
            ->method('copy')
            ->will($this->returnValue($clone));

        $this->assertSame($clone, $command->apply($image));
    }
}
