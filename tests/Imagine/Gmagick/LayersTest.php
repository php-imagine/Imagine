<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Gmagick;

use Imagine\Image\AbstractLayersTest;

class LayersTest extends AbstractLayersTest
{
    protected function setUp()
    {
        parent::setUp();

        if (!class_exists('Gmagick')) {
            $this->markTestSkipped('Gmagick is not installed');
        }
    }

    public function testCount()
    {
        $resource = $this->getMockBuilder('\Gmagick')
            ->disableOriginalConstructor()
            ->getMock();

        $resource->expects($this->once())
            ->method('getnumberimages')
            ->will($this->returnValue(42));

        $layers = new Layers(new Image($resource), $resource);

        $this->assertCount(42, $layers);
    }

    public function testGetLayer()
    {
        $resource = $this->getMockBuilder('\Gmagick')
            ->disableOriginalConstructor()
            ->getMock();

        $resource->expects($this->any())
            ->method('getnumberimages')
            ->will($this->returnValue(2));

        $layer = $this->getMockBuilder('\Gmagick')
            ->disableOriginalConstructor()
            ->getMock();

        $resource->expects($this->any())
            ->method('getimage')
            ->will($this->returnValue($layer));

        $layers = new Layers(new Image($resource), $resource);

        foreach($layers as $layer) {
            $this->assertInstanceOf('Imagine\Image\ImageInterface', $layer);
        }
    }

    public function getImagine()
    {
        return new Imagine();
    }
}
