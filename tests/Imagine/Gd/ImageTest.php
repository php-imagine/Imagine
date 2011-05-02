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

use Imagine\Image\Color;

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
            ->with($this->resource, $start, $box, $start)
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
            ->with($this->resource, $start, $box, $box->position('top', 'left'))
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
        $this->expectDisableAlphaBlending($this->resource);

        $this->resource->expects($this->once())
            ->method('box')
            ->will($this->returnValue($size));

        $this->expectEnableAlphaBlending($resource);
        $this->expectEnableAlphaBlending($this->resource);

        $this->resource->expects($this->once())
            ->method('copy')
            ->with($resource, $box->position('top', 'left'), $box, $start);

        $this->image->paste($image, $start);
    }

    public function testShouldResize()
    {
        $current = new Box(100, 100);
        $target  = new Box(200, 200);
        $resized = $this->getResource();

        $this->gd->expects($this->once())
            ->method('create')
            ->with($target)
            ->will($this->returnValue($resized));

        $this->expectTransparencyToBeEnabled($resized);

        $resized->expects($this->once())
            ->method('copyResized')
            ->with($this->resource, $current->position('top', 'left'), $current, $target->position('top', 'left'), $target)
            ->will($this->returnValue(true));

        $this->resource->expects($this->once())
            ->method('box')
            ->will($this->returnValue($current));

        $this->resource->expects($this->once())
            ->method('destroy');

        $this->image->resize($target);
    }

    public function testShouldRotateAndReplaceResource()
    {
        $angle = 90;
        $color = new Color('000');

        $this->resource->expects($this->once())
            ->method('rotate')
            ->with($angle, $color)
            ->will($this->returnValue(true));

        $this->image->rotate($angle, $color);
    }

    /**
     * @dataProvider getPathsAndSaveMethods
     *
     * @param string $path
     * @param string $method
     */
    public function testShouldSaveImage($path, $method)
    {
        $this->resource->expects($this->once())
            ->method($method)
            ->with($path);

        $this->image->save($path);
    }

    public function getPathsAndSaveMethods()
    {
        return array(
            array('/path/to/image.jpg', 'jpeg'),
            array('/path/to/image.jpeg', 'jpeg'),
            array('/path/to/image.png', 'png'),
            array('/path/to/image.gif', 'gif'),
            array('/path/to/image.wbmp', 'wbmp'),
            array('/path/to/image.xbm', 'xbm'),
        );
    }

    public function testShouldSaveWithJpegOptions()
    {
        $path    = '/path/to/image.jpg';
        $quality = 100;

        $this->resource->expects($this->once())
            ->method('jpeg')
            ->with($path, $quality);

        $this->image->save($path, array('quality' => $quality));
    }

    public function testShouldSaveWithPngOptions()
    {
        $path    = '/path/to/image.png';
        $quality = 100;

        $this->resource->expects($this->once())
            ->method('png')
            ->with($path, 9);

        $this->image->save($path, array('quality' => $quality));
    }

    public function testShouldFlipHorizontallyImage()
    {
        $box      = new Box(100, 100);
        $resource = $this->getResource();

        $this->resource->expects($this->once())
            ->method('box')
            ->will($this->returnValue($box));

        $this->gd->expects($this->once())
            ->method('create')
            ->with($box)
            ->will($this->returnValue($resource));

        $this->expectTransparencyToBeEnabled($resource);

        $resource->expects($this->exactly($box->getWidth()))
            ->method('copy')
            ->will($this->returnValue(true));

        $this->resource->expects($this->once())
            ->method('destroy');

        $this->image->flipHorizontally();
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
