<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine;

use Imagine\Cartesian\Coordinate;
use Imagine\Cartesian\Coordinate\Center;
use Imagine\Cartesian\Size;

abstract class AbstractImageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Imagine\ImagineInterface::open
     * @covers Imagine\ImageInterface::paste
     * @covers Imagine\ImageInterface::copy
     * @covers Imagine\ImageInterface::resize
     * @covers Imagine\ImageInterface::flipVertically
     * @covers Imagine\ImageInterface::save
     */
    public function testRotate()
    {
        $factory = $this->getImagine();

        $image = $factory->open('tests/Imagine/Fixtures/google.png');
        $size  = $image->getSize();

        $image->paste(
                $image->copy()
                    ->resize($size->scale(0.5))
                    ->flipVertically(),
                new Center($size)
            )
            ->save('tests/Imagine/Fixtures/clone.jpg', array('quality' => 100));

        unset($image);

        $image = $factory->open('tests/Imagine/Fixtures/clone.jpg');
        $size  = $image->getSize();

        $this->assertEquals(364, $size->getWidth());
        $this->assertEquals(126, $size->getHeight());

        unlink('tests/Imagine/Fixtures/clone.jpg');
    }

    /**
     * @covers Imagine\ImagineInterface::open
     * @covers Imagine\ImageInterface::thumbnail
     * @covers Imagine\ImageInterface::save
     */
    public function testThumbnailGeneration()
    {
        $factory = $this->getImagine();

        $image = $factory->open('tests/Imagine/Fixtures/google.png');

        $image->thumbnail(new Size(50, 50), ImageInterface::THUMBNAIL_INSET, new Color('fff'))
            ->save('tests/Imagine/Fixtures/inset.png', array('quality' => 9));

        $image->thumbnail(new Size(50, 50), ImageInterface::THUMBNAIL_OUTBOUND)
            ->save('tests/Imagine/Fixtures/outbound.png', array('quality' => 9));

        $thumbnail = $factory->open('tests/Imagine/Fixtures/inset.png');
        $size      = $thumbnail->getSize();

        $this->assertEquals(50, $size->getWidth());
        $this->assertEquals(17, $size->getHeight());
        unlink('tests/Imagine/Fixtures/inset.png');

        $thumbnail = $factory->open('tests/Imagine/Fixtures/outbound.png');
        $size      = $thumbnail->getSize();

        $this->assertEquals(50, $size->getWidth());
        $this->assertEquals(50, $size->getHeight());
        unlink('tests/Imagine/Fixtures/outbound.png');
    }

    /**
     * @covers Imagine\ImagineInterface::open
     * @covers Imagine\ImageInterface::resize
     * @covers Imagine\ImageInterface::flipHorizontally
     * @covers Imagine\ImageInterface::save
     */
    public function testCropResizeFlip()
    {
        $factory = $this->getImagine();

        $image = $factory->open('tests/Imagine/Fixtures/google.png');

        $this->assertSame($image, $image->crop(new Coordinate(0, 0), new Size(126, 126))
            ->resize(new Size(200, 200))
            ->flipHorizontally()
            ->save('tests/Imagine/Fixtures/flop.png'));

        $size = $image->getSize();

        $this->assertEquals(200, $size->getWidth());
        $this->assertEquals(200, $size->getHeight());

        unset($image);

        $image = $factory->open('tests/Imagine/Fixtures/flop.png');
        $size  = $image->getSize();

        $this->assertEquals(200, $size->getWidth());
        $this->assertEquals(200, $size->getHeight());

        unset($image);

        unlink('tests/Imagine/Fixtures/flop.png');
    }

    /**
     * @covers Imagine\ImagineInterface::create
     * @covers Imagine\ImageInterface::save
     */
    public function testCreateAndSaveEmptyImage()
    {
        $factory = $this->getImagine();

        $factory->create(new Size(400, 300), new Color('000'))
            ->save('tests/Imagine/Fixtures/blank.png', array('quality' => 100));

        $image = $factory->open('tests/Imagine/Fixtures/blank.png');
        $size  = $image->getSize();

        $this->assertEquals(400, $size->getWidth());
        $this->assertEquals(300, $size->getHeight());

        unlink('tests/Imagine/Fixtures/blank.png');
    }

    abstract protected function getImagine();
}
