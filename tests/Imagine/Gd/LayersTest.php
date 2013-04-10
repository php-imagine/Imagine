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

use Imagine\Image\AbstractLayersTest;
use Imagine\Image\ImageInterface;

class LayersTest extends AbstractLayersTest
{
    protected function setUp()
    {
        parent::setUp();

        if (!function_exists('gd_info')) {
            $this->markTestSkipped('Gd not installed');
        }
    }

    public function testCount()
    {
        $resource = imagecreate(20, 20);
        $layers = new Layers(new Image($resource), $resource);

        $this->assertCount(1, $layers);
    }

    public function testGetLayer()
    {
        $resource = imagecreate(20, 20);
        $layers = new Layers(new Image($resource), $resource);

        foreach($layers as $layer) {
            $this->assertInstanceOf('Imagine\Image\ImageInterface', $layer);
        }
    }

    public function testLayerArrayAccess()
    {
        $resource = $this->getResource();
        $secondResource = $this->getResource();
        $thirdResource = $this->getResource();

        $layers = $this->getLayers($this->getImage($resource), $resource);

        $this->assertEquals($resource, $layers[0]);
        $this->assertTrue(isset($layers[0]));
    }

    public function testLayerArrayAccessInvalidArgumentExceptions($offset = null)
    {
        $this->markTestSkipped('Gd does not fully supports layers array access');
    }

    public function testLayerArrayAccessOutOfBoundsExceptions($offset = null)
    {
        $this->markTestSkipped('Gd does not fully supports layers array access');
    }

    public function getImage($resource)
    {
        return new Image($resource);
    }

    public function getLayers(ImageInterface $image, $resource)
    {
        return new Layers($image, $resource);
    }

    public function getResource()
    {
        return imagecreatetruecolor(10, 10);
    }

    public function getImagine()
    {
        return new Imagine();
    }
}
