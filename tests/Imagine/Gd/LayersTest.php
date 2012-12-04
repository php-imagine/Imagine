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

    public function getImagine()
    {
        return new Imagine();
    }
}
