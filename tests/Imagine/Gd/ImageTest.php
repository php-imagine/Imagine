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

use Imagine\Image\AbstractImageTest;
use Imagine\Image\Color;
use Imagine\Image\ImageInterface;

class ImageTest extends AbstractImageTest
{
    protected function setUp()
    {
        parent::setUp();

        if (!function_exists('gd_info')) {
            $this->markTestSkipped('Gd not installed');
        }
    }

    public function testImageResolutionChange() {
        $this->markTestSkipped('GD driver does not support resolution options');
    }

    public function testGdExtension()
    {
        $resource = imagecreatetruecolor(10, 10);

        $image = new ExtendedGD($resource);
        $this->assertEquals($resource, $image->getResource());
    }

    protected function getImagine()
    {
        return new Imagine();
    }

    protected function supportMultipleLayers()
    {
        return false;
    }
}

class ExtendedGD extends Image
{
    public function getResource()
    {
        return $this->resource;
    }
}
