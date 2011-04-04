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

use Imagine\AbstractImagineTest;
use Imagine\Image\Box;
use Imagine\Image\Color;

class ImagineTest extends GdTestCase
{
    private $gd;
    private $imagine;

    protected function setUp()
    {
        parent::setUp();

        $this->gd      = $this->getGd();
        $this->imagine = new Imagine($this->gd);
    }

    /**
     * @expectedException Imagine\Exception\RuntimeException
     */
    public function testShouldThrowOnFailedCreate()
    {
        $width    = 100;
        $height   = 100;

        $this->gd->expects($this->once())
            ->method('create')
            ->with($width, $height)
            ->will($this->returnValue(null));

        $this->imagine->create(new Box($width, $height));
    }

    /**
     * @expectedException Imagine\Exception\RuntimeException
     */
    public function testShouldThrowOnCreateOnFailedAlphaBlending()
    {
        $width    = 100;
        $height   = 100;
        $resource = $this->getResource();

        $this->gd->expects($this->once())
            ->method('create')
            ->with($width, $height)
            ->will($this->returnValue($resource));

        $this->expectAlphaBlending($resource, false);

        $this->imagine->create(new Box($width, $height));
    }

    /**
     * @expectedException Imagine\Exception\RuntimeException
     */
    public function testShouldThrowOnCreateOnFailedSaveAlpha()
    {
        $width    = 100;
        $height   = 100;
        $resource = $this->getResource();

        $this->gd->expects($this->once())
            ->method('create')
            ->with($width, $height)
            ->will($this->returnValue($resource));

        $this->expectAlphaBlending($resource, true);
        $this->expectSaveAlpha($resource, false);

        $this->imagine->create(new Box($width, $height));
    }

    /**
     * @expectedException Imagine\Exception\RuntimeException
     */
    public function testShouldThrowOnCreateOnFailedColorAllocate()
    {
        $width    = 100;
        $height   = 100;
        $resource = $this->getResource();

        $this->gd->expects($this->once())
            ->method('create')
            ->with($width, $height)
            ->will($this->returnValue($resource));

        $this->expectTransparencyToBeEnabled($resource);
        $this->expectColorAllocateAlpha($resource, false);

        $this->imagine->create(new Box($width, $height));
    }

    /**
     * @expectedException Imagine\Exception\RuntimeException
     */
    public function testShouldThrowOnCreateOnFailedFilledRectangle()
    {
        $index    = 10;
        $width    = 100;
        $height   = 100;
        $resource = $this->getResource();

        $this->gd->expects($this->once())
            ->method('create')
            ->with($width, $height)
            ->will($this->returnValue($resource));

        $this->expectTransparencyToBeEnabled($resource);
        $this->expectColorAllocateAlpha($resource, $index);
        $this->expectFilledRectangle($resource, $width, $height, $index, false);

        $this->imagine->create(new Box($width, $height));
    }


    public function testShouldCreateImage()
    {
        $index    = 10;
        $width    = 100;
        $height   = 100;
        $resource = $this->getResource();

        $this->gd->expects($this->once())
            ->method('create')
            ->with($width, $height)
            ->will($this->returnValue($resource));

        $this->expectTransparencyToBeEnabled($resource);
        $this->expectColorAllocateAlpha($resource, $index);
        $this->expectFilledRectangle($resource, $width, $height, $index, true);

        $image = $this->imagine->create(new Box($width, $height));

        $this->assertInstanceOf('Imagine\Gd\Image', $image);
    }

    /**
     * @expectedException Imagine\Exception\InvalidArgumentException
     */
    public function testShouldNotOpenImageFromInvalidPath()
    {
        $this->imagine->open('/some/path/to/image.jpg');
    }

    /**
     * @expectedException Imagine\Exception\RuntimeException
     */
    public function testShouldThrowOnFailedOpen()
    {
        $path = 'tests/Imagine/Fixtures/google.png';

        $this->gd->expects($this->once())
            ->method('open')
            ->with($path)
            ->will($this->returnValue(null));

        $this->imagine->open($path);
    }

    /**
     * @expectedException Imagine\Exception\RuntimeException
     */
    public function testShouldThrowOnOpenOnFailedAlphaBlending()
    {
        $path     = 'tests/Imagine/Fixtures/google.png';
        $resource = $this->getResource();

        $this->gd->expects($this->once())
            ->method('open')
            ->with($path)
            ->will($this->returnValue($resource));

        $this->expectAlphaBlending($resource, false);

        $this->imagine->open($path);
    }

    /**
     * @expectedException Imagine\Exception\RuntimeException
     */
    public function testShouldThrowOnOpenOnFailedSaveAlpha()
    {
        $path     = 'tests/Imagine/Fixtures/google.png';
        $resource = $this->getResource();

        $this->gd->expects($this->once())
            ->method('open')
            ->with($path)
            ->will($this->returnValue($resource));

        $this->expectAlphaBlending($resource, true);
        $this->expectSaveAlpha($resource, false);

        $this->imagine->open($path);
    }

    public function testShouldOpenImage()
    {
        $path     = 'tests/Imagine/Fixtures/google.png';
        $resource = $this->getResource();

        $this->gd->expects($this->once())
            ->method('open')
            ->with($path)
            ->will($this->returnValue($resource));

        $this->expectTransparencyToBeEnabled($resource);

        $image = $this->imagine->open($path);

        $this->assertInstanceOf('Imagine\Gd\Image', $image);
    }

    /**
     * @expectedException Imagine\Exception\RuntimeException
     */
    public function testShouldThrowOnFailedLoad()
    {
        $string   = 'foo';

        $this->gd->expects($this->once())
            ->method('load')
            ->with($string)
            ->will($this->returnValue(null));

        $this->imagine->load($string);
    }

    /**
     * @expectedException Imagine\Exception\RuntimeException
     */
    public function testShouldThrowOnLoadOnFailedAlphaBlending()
    {
        $string   = 'foo';
        $resource = $this->getResource();

        $this->gd->expects($this->once())
            ->method('load')
            ->with($string)
            ->will($this->returnValue($resource));

        $this->expectAlphaBlending($resource, false);

        $this->imagine->load($string);
    }

    /**
     * @expectedException Imagine\Exception\RuntimeException
     */
    public function testShouldThrowOnLoadOnFailedSaveAlpha()
    {
        $string   = 'foo';
        $resource = $this->getResource();

        $this->gd->expects($this->once())
            ->method('load')
            ->with($string)
            ->will($this->returnValue($resource));

        $this->expectAlphaBlending($resource, true);
        $this->expectSaveAlpha($resource, false);

        $this->imagine->load($string);
    }

    public function testShouldLoadImageFromString()
    {
        $string   = 'foo';
        $resource = $this->getResource();

        $this->gd->expects($this->once())
            ->method('load')
            ->with($string)
            ->will($this->returnValue($resource));

        $this->expectTransparencyToBeEnabled($resource);

        $image = $this->imagine->load($string);

        $this->assertInstanceOf('Imagine\Gd\Image', $image);
    }

    /**
     * Sets transparency related expectations
     *
     * @param PHPUnit_Framework_MockObject_MockObject $resource
     */
    private function expectTransparencyToBeEnabled(\PHPUnit_Framework_MockObject_MockObject $resource)
    {
        $this->expectSaveAlpha($resource);
        $this->expectAlphaBlending($resource);
    }

    /**
     * @param PHPUnit_Framework_MockObject_MockObject $resource
     * @param integer                                 $index
     */
    private function expectColorAllocateAlpha(\PHPUnit_Framework_MockObject_MockObject $resource, $index, $red = 255, $green = 255, $blue = 255, $alpha = 0)
    {
        $resource->expects($this->once())
            ->method('colorallocatealpha')
            ->with($red, $green, $blue, $alpha)
            ->will($this->returnValue($index));
    }

    /**
     * @param PHPUnit_Framework_MockObject_MockObject $resource
     * @param boolean                                 $result
     */
    private function expectAlphaBlending(\PHPUnit_Framework_MockObject_MockObject $resource, $result = true)
    {
        $resource->expects($this->once())
            ->method('alphablending')
            ->with(false)
            ->will($this->returnValue($result));
    }

    /**
     * @param PHPUnit_Framework_MockObject_MockObject $resource
     * @param boolean                                 $result
     */
    private function expectSaveAlpha(\PHPUnit_Framework_MockObject_MockObject $resource, $result = true)
    {
        $resource->expects($this->once())
            ->method('savealpha')
            ->with(true)
            ->will($this->returnValue($result));
    }

    /**
     * @param PHPUnit_Framework_MockObject_MockObject $resource
     * @param boolean                                 $result
     */
    private function expectFilledRectangle(\PHPUnit_Framework_MockObject_MockObject $resource, $width, $height, $index, $result = true)
    {
        $resource->expects($this->once())
            ->method('filledrectangle')
            ->with(0, 0, $width, $height, $index)
            ->will($this->returnValue($result));
    }
}
