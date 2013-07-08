<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Test\Imagick;

use Imagine\Imagick\Image;
use Imagine\Imagick\Layers;
use Imagine\Imagick\Imagine;
use Imagine\Test\Image\AbstractLayersTest;
use Imagine\Image\ImageInterface;

class LayersTest extends AbstractLayersTest
{
    protected function setUp()
    {
        parent::setUp();

        if (!class_exists('Imagick')) {
            $this->markTestSkipped('Imagick is not installed');
        }
    }

    public function testCount()
    {
        $resource = $this->getMockBuilder('\Imagick')
            ->disableOriginalConstructor()
            ->getMock();

        $resource->expects($this->once())
            ->method('getNumberImages')
            ->will($this->returnValue(42));

        $layers = new Layers(new Image($resource), $resource);

        $this->assertCount(42, $layers);
    }

    public function testGetLayer()
    {
        $resource = $this->getMockBuilder('\Imagick')
            ->disableOriginalConstructor()
            ->getMock();

        $resource->expects($this->any())
            ->method('getNumberImages')
            ->will($this->returnValue(2));

        $layer = $this->getMockBuilder('\Imagick')
            ->disableOriginalConstructor()
            ->getMock();

        $resource->expects($this->any())
            ->method('getImage')
            ->will($this->returnValue($layer));

        $layers = new Layers(new Image($resource), $resource);

        foreach ($layers as $layer) {
            $this->assertInstanceOf('Imagine\Image\ImageInterface', $layer);
        }
    }

    public function testCoalesce()
    {
        $width = null;
        $height = null;

        $resource = new \Imagick;
        $resource->newImage(20, 10, new \ImagickPixel("black"));
        $resource->newImage(10, 10, new \ImagickPixel("black"));

        $layers = new Layers(new Image($resource), $resource);
        $layers->coalesce();

        foreach ($layers as $layer) {
            $size = $layer->getSize();

            if ($width === null) {
                $width = $size->getWidth();
            } else {
                $this->assertEquals($width, $size->getWidth());
            }

            if ($height === null) {
                $height = $size->getHeight();
            } else {
                $this->assertEquals($height, $size->getHeight());
            }
        }
    }

    public function getImage($path = null)
    {
        if ($path) {
            return new Image(new \Imagick($path));
        } else {
            return new Image(new \Imagick());
        }
    }

    protected function getImagine()
    {
        return new Imagine();
    }

    protected function assertLayersEquals($expected, $actual)
    {
        $this->assertEquals($expected->getImagick(), $actual->getImagick());
    }
}
