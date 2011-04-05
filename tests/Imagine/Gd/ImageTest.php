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
        $width  = 100;
        $height = 100;
        $copy   = $this->getResource();

        $this->resource->expects($this->once())
            ->method('sx')
            ->will($this->returnValue($width));
        $this->resource->expects($this->once())
            ->method('sy')
            ->will($this->returnValue($height));

        $this->gd->expects($this->once())
            ->method('create')
            ->with($width, $height)
            ->will($this->returnValue($copy));

        $this->expectTransparencyToBeEnabled($copy);

        $this->resource->expects($this->once())
            ->method('copymerge')
            ->with($copy, 0, 0, 0, 0, $width, $height, 100)
            ->will($this->returnValue(true));

        $image = $this->image->copy();

        $this->assertInstanceOf('Imagine\ImageInterface', $image);
    }

    public function testShouldCropImage()
    {
        $x      = 0;
        $y      = 0;
        $width  = 100;
        $height = 100;
        $crop   = $this->getResource();

        $this->gd->expects($this->once())
            ->method('create')
            ->with($width, $height)
            ->will($this->returnValue($crop));

        $this->expectTransparencyToBeEnabled($crop);

        $this->resource->expects($this->once())
            ->method('sx')
            ->will($this->returnValue($width * 2));
        $this->resource->expects($this->once())
            ->method('sy')
            ->will($this->returnValue($height * 2));
        $this->resource->expects($this->once())
            ->method('copymerge')
            ->with($crop, 0, 0, $x, $y, $width, $height, 100)
            ->will($this->returnValue(true));
        $this->resource->expects($this->once())
            ->method('destroy');

        $crop = $this->image->crop(new Point($x, $y), new Box($width, $height));
    }
}
