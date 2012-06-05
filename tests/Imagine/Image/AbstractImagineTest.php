<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Image;

use Imagine\Test\ImagineTestCase;

abstract class AbstractImagineTest extends ImagineTestCase
{
    public function testShouldCreateEmptyImage()
    {
        $factory = $this->getImagine();
        $image   = $factory->create(new Box(50, 50));
        $size    = $image->getSize();

        $this->assertInstanceOf('Imagine\Image\ImageInterface', $image);
        $this->assertEquals(50, $size->getWidth());
        $this->assertEquals(50, $size->getHeight());
    }

    public function testShouldOpenAnImage()
    {
        $factory = $this->getImagine();
        $image   = $factory->open('tests/Imagine/Fixtures/google.png');
        $size    = $image->getSize();

        $this->assertInstanceOf('Imagine\Image\ImageInterface', $image);
        $this->assertEquals(364, $size->getWidth());
        $this->assertEquals(126, $size->getHeight());
    }

    public function testShouldCreateImageFromString()
    {
        $factory = $this->getImagine();
        $image   = $factory->load(file_get_contents('tests/Imagine/Fixtures/google.png'));
        $size    = $image->getSize();

        $this->assertInstanceOf('Imagine\Image\ImageInterface', $image);
        $this->assertEquals(364, $size->getWidth());
        $this->assertEquals(126, $size->getHeight());
    }

    public function testShouldCreateImageFromResource()
    {
        $factory = $this->getImagine();
        $resource = fopen('tests/Imagine/Fixtures/google.png', 'r');
        $image   = $factory->read($resource);
        $size    = $image->getSize();

        $this->assertInstanceOf('Imagine\Image\ImageInterface', $image);
        $this->assertEquals(364, $size->getWidth());
        $this->assertEquals(126, $size->getHeight());
    }

    public function testShouldDetermineFontSize()
    {
        if (!$this->isFontTestSupported()) {
            $this->markTestSkipped('This install does not support font tests');
        }
        
        $path    = 'tests/Imagine/Fixtures/font/Arial.ttf';
        $black   = new Color('000');
        $factory = $this->getImagine();

        $this->assertEquals($this->getEstimatedFontBox(), $factory->font($path, 36, $black)->box('string'));
    }

    abstract protected function getEstimatedFontBox();

    abstract protected function getImagine();
    
    abstract protected function isFontTestSupported();
}
