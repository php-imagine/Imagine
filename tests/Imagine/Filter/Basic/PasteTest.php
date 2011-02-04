<?php

namespace Imagine\Filter\Basic;

class PasteTest extends BasicFilterTestCase
{
    public function testShouldFlipImage()
    {
        $x       = 0;
        $y       = 0;
        $image   = $this->getImage();
        $toPaste = $this->getImage();
        $filter  = new Paste($toPaste, $x, $y);

        $image->expects($this->once())
            ->method('paste')
            ->with($toPaste, $x, $y)
            ->will($this->returnValue($image));

        $this->assertSame($image, $filter->apply($image));
    }
}
