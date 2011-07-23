<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Filter\Basic;

use Imagine\Image\Box;
use Imagine\ImageInterface;
use Imagine\Filter\FilterTestCase;

use Imagine\Gd\Imagine;

class ThumbnailTest extends FilterTestCase
{
    public function testShouldMakeAThumbnail()
    {
        $image     = $this->getImage();
        $thumbnail = $this->getImage();
        $size      = new Box(50, 50);
        $filter    = new Thumbnail($size);

        $image->expects($this->once())
            ->method('thumbnail')
            ->with($size, ImageInterface::THUMBNAIL_INSET, true)
            ->will($this->returnValue($thumbnail));

        $this->assertSame($thumbnail, $filter->apply($image));
    }

    public function testShouldScaleUp()
    {
        if (!function_exists('gd_info')) {
            $this->markTestSkipped('Gd not installed');
        }
        $size    = new Box(1000, 1000);
        $imagine = new Imagine();

        $imagine->open('tests/Imagine/Fixtures/large.jpg')
            ->thumbnail($size, ImageInterface::THUMBNAIL_INSET,true)
            ->save('tests/Imagine/Fixtures/thumbnail.jpg');

        $this->assertTrue(file_exists('tests/Imagine/Fixtures/thumbnail.jpg'));

        $generated = $imagine->open('tests/Imagine/Fixtures/thumbnail.jpg')
            ->getSize();
        $original = $imagine->open('tests/Imagine/Fixtures/large.jpg')
            ->getSize();

        $this->assertNotEquals(
            $size,
            $generated
        );
        $this->assertTrue($generated->getWidth() >= $original->getWidth());
        $this->assertTrue($generated->getHeight() >= $original->getHeight());
        unlink('tests/Imagine/Fixtures/thumbnail.jpg');
    }

    public function testShouldNotScaleUp()
    {
        if (!function_exists('gd_info')) {
            $this->markTestSkipped('Gd not installed');
        }
        $size    = new Box(1000, 1000);
        $imagine = new Imagine();

        $imagine->open('tests/Imagine/Fixtures/large.jpg')
            ->thumbnail($size, ImageInterface::THUMBNAIL_INSET,false)
            ->save('tests/Imagine/Fixtures/thumbnail.jpg');

        $this->assertTrue(file_exists('tests/Imagine/Fixtures/thumbnail.jpg'));

        $generated = $imagine->open('tests/Imagine/Fixtures/thumbnail.jpg')
            ->getSize();
        $original = $imagine->open('tests/Imagine/Fixtures/large.jpg')
            ->getSize();

        $this->assertNotEquals(
            $size,
            $generated
        );
        $this->assertTrue($generated->getWidth() <= $original->getWidth());
        $this->assertTrue($generated->getHeight() <= $original->getHeight());
        unlink('tests/Imagine/Fixtures/thumbnail.jpg');
    }
}
