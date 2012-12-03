<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Imagick;

use Imagine\Image\AbstractLayersTest;

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

        foreach($layers as $layer) {
            $this->assertInstanceOf('Imagine\Image\ImageInterface', $layer);
        }
    }

    protected function getImagine()
    {
        return new Imagine();
    }
}
