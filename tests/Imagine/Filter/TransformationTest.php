<?php

namespace Imagine\Filter;

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Imagine\Color;
use Imagine\Box;
use Imagine\ImageInterface;

class TransformationTest extends FilterTestCase
{
    public function testSimpleStack()
    {
        $image = $this->getImage();
        $size  = new Box(50, 50);
        $path  = sys_get_temp_dir();

        $image->expects($this->once())
            ->method('resize')
            ->with($size)
            ->will($this->returnValue($image));

        $image->expects($this->once())
            ->method('save')
            ->with($path)
            ->will($this->returnValue($image));

        $transformation = new Transformation();
        $this->assertSame($image, $transformation->resize($size)
            ->save($path)
            ->apply($image)
        );
    }

    public function testComplexFlow()
    {
        $image      = $this->getImage();
        $clone      = $this->getImage();
        $thumbnail  = $this->getImage();
        $path       = sys_get_temp_dir();
        $size       = new Box(50, 50);
        $resize     = new Box(200, 200);
        $angle      = 90;
        $background = new Color('fff');

        $image->expects($this->once())
            ->method('resize')
            ->with($resize)
            ->will($this->returnValue($image));

        $image->expects($this->once())
            ->method('copy')
            ->will($this->returnValue($clone));

        $clone->expects($this->once())
            ->method('rotate')
            ->with($angle, $background)
            ->will($this->returnValue($clone));

        $clone->expects($this->once())
            ->method('thumbnail')
            ->with($size, ImageInterface::THUMBNAIL_INSET)
            ->will($this->returnValue($thumbnail));

        $thumbnail->expects($this->once())
            ->method('save')
            ->with($path)
            ->will($this->returnValue($thumbnail));

        $transformation = new Transformation();

        $transformation->resize($resize)
            ->copy()
            ->rotate($angle, $background)
            ->thumbnail($size, ImageInterface::THUMBNAIL_INSET)
            ->save($path);

        $this->assertSame($thumbnail, $transformation->apply($image));
    }
}
