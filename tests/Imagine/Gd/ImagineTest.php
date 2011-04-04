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

class ImagineTest extends \PHPUnit_Framework_TestCase
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
     * @expectedException Imagine\Exception\InvalidArgumentException
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

    public function testShouldCreateImage()
    {
        $color    = 10;
        $width    = 100;
        $height   = 100;
        $resource = $this->getResource();

        $this->gd->expects($this->once())
            ->method('create')
            ->with($width, $height)
            ->will($this->returnValue($resource));

        $this->expectTransparencyToBeEnabled($resource);

        $resource->expects($this->once())
            ->method('colorallocatealpha')
            ->with(255, 255, 255, 0)
            ->will($this->returnValue($color));
        $resource->expects($this->once())
            ->method('filledrectangle')
            ->with(0, 0, $width, $height, $color)
            ->will($this->returnValue(true));

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
     * @expectedException Imagine\Exception\InvalidArgumentException
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
     * @expectedException Imagine\Exception\InvalidArgumentException
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
        $resource->expects($this->once())
            ->method('savealpha')
            ->with(true)
            ->will($this->returnValue(true));
        $resource->expects($this->once())
            ->method('alphablending')
            ->with(false)
            ->will($this->returnValue(true));
    }

    private function getResource()
    {
        return $this->getMock('Imagine\Gd\ResourceInterface');
    }

    private function getGd()
    {
        return $this->getMock('Imagine\Gd\GdInterface');
    }
}
