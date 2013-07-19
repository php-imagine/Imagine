<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Test\Filter;

use Imagine\Filter\Transformation;
use Imagine\Image\Box;
use Imagine\Image\Point;
use Imagine\Image\ManipulatorInterface;

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
        $background = $this->getPalette()->color('fff');

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
            ->with($size, ManipulatorInterface::THUMBNAIL_INSET)
            ->will($this->returnValue($thumbnail));

        $thumbnail->expects($this->once())
            ->method('save')
            ->with($path)
            ->will($this->returnValue($thumbnail));

        $transformation = new Transformation();

        $transformation->resize($resize)
            ->copy()
            ->rotate($angle, $background)
            ->thumbnail($size, ManipulatorInterface::THUMBNAIL_INSET)
            ->save($path);

        $this->assertSame($thumbnail, $transformation->apply($image));
    }

    public function testCropFlipPasteShow()
    {
        $img1  = $this->getImage();
        $img2  = $this->getImage();
        $start = new Point(0, 0);
        $size  = new Box(50, 50);

        $img1->expects($this->once())
            ->method('paste')
            ->with($img2, $start)
            ->will($this->returnValue($img1));

        $img1->expects($this->once())
            ->method('show')
            ->with('png')
            ->will($this->returnValue($img1));

        $img2->expects($this->once())
            ->method('flipHorizontally')
            ->will($this->returnValue($img2));

        $img2->expects($this->once())
            ->method('flipVertically')
            ->will($this->returnValue($img2));

        $img2->expects($this->once())
            ->method('crop')
            ->with($start, $size)
            ->will($this->returnValue($img2));

        $transformation2 = new Transformation();
        $transformation2->flipHorizontally()
            ->flipVertically()
            ->crop($start, $size);

        $transformation1 = new Transformation();
        $transformation1->paste($transformation2->apply($img2), $start)
            ->show('png')
            ->apply($img1);
    }
}
