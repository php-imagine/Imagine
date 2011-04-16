<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Gd;

use Imagine\Image\Box;

use Imagine\Image\Point;

class ImageTest extends TestCase
{
    private $gd;
    private $resource;
    private $image;

    protected function setUp()
    {
        parent::setUp();

        $this->gd       = $this->getGd();
        $this->resource = $this->getResource();
        $this->image    = new Image($this->gd, $this->resource);
    }

    public function testShouldCopyImage()
    {
        $box   = new Box(100, 100);
        $start = $box->position('top', 'left');
        $copy  = $this->getResource();

        $this->resource->expects($this->once())
            ->method('box')
            ->will($this->returnValue($box));

        $this->gd->expects($this->once())
            ->method('create')
            ->with($box)
            ->will($this->returnValue($copy));

        $this->expectTransparencyToBeEnabled($copy);

        $copy->expects($this->once())
            ->method('copy')
            ->with($this->resource, $box, $start, $start)
            ->will($this->returnValue(true));

        $image = $this->image->copy();

        $this->assertInstanceOf('Imagine\ImageInterface', $image);
    }

    public function testShouldCropImage()
    {
        $start  = new Point(0, 0);
        $box    = new Box(100, 100);
        $crop   = $this->getResource();

        $this->gd->expects($this->once())
            ->method('create')
            ->with($box)
            ->will($this->returnValue($crop));

        $this->expectTransparencyToBeEnabled($crop);

        $crop->expects($this->once())
            ->method('copy')
            ->with($this->resource, $box, $start, $box->position('top', 'left'))
            ->will($this->returnValue(true));

        $this->resource->expects($this->once())
            ->method('box')
            ->will($this->returnValue($box->scale(2)));
        $this->resource->expects($this->once())
            ->method('destroy');

        $crop = $this->image->crop($start, $box);
    }

    public function testShouldPasteImage()
    {
        $size     = new Box(100, 100);
        $box      = new Box(100, 100);
        $start    = new Point(0, 0);
        $resource = $this->getResource();
        $image    = new Image($this->gd, $resource);

        $resource->expects($this->once())
            ->method('box')
            ->will($this->returnValue($box));

        $this->expectDisableAlphaBlending($resource);
        $this->expectEnableAlphaBlending($resource);

        $this->resource->expects($this->once())
            ->method('box')
            ->will($this->returnValue($size));

        $this->expectDisableAlphaBlending($this->resource);
        $this->expectEnableAlphaBlending($this->resource);

        $this->resource->expects($this->once())
            ->method('copy')
            ->with($resource, $box, $box->position('top', 'left'), $start);

        $this->image->paste($image, $start);
    }

    /**
     * @param PHPUnit_Framework_MockObject_MockObject $resource
     * @param boolean                                 $result
     */
    protected function expectEnableAlphaBlending(\PHPUnit_Framework_MockObject_MockObject $resource, $result = true)
    {
        $resource->expects($this->once())
            ->method('enableAlphaBlending')
            ->will($this->returnValue($result));
    }
}
