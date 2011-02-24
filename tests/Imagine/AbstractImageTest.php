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

use Imagine\Mask\Gradient\Horizontal;

use Imagine\Mask\Gradient\Vertical;

use Imagine\Point;
use Imagine\Point\Center;
use Imagine\Box;

abstract class AbstractImageTest extends \PHPUnit_Framework_TestCase
{
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

    public function testThumbnailGeneration()
    {
        $factory = $this->getImagine();

        $image = $factory->open('tests/Imagine/Fixtures/google.png');

        $image->thumbnail(new Box(50, 50), ImageInterface::THUMBNAIL_INSET, new Color('fff'))
            ->save('tests/Imagine/Fixtures/inset.png', array('quality' => 9));

        $image->thumbnail(new Box(50, 50), ImageInterface::THUMBNAIL_OUTBOUND)
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

    public function testCropResizeFlip()
    {
        $factory = $this->getImagine();

        $image = $factory->open('tests/Imagine/Fixtures/google.png');

        $this->assertSame($image, $image->crop(new Point(0, 0), new Box(126, 126))
            ->resize(new Box(200, 200))
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

    public function testCreateAndSaveEmptyImage()
    {
        $factory = $this->getImagine();

        $factory->create(new Box(400, 300), new Color('000'))
            ->save('tests/Imagine/Fixtures/blank.png', array('quality' => 100));

        $image = $factory->open('tests/Imagine/Fixtures/blank.png');
        $size  = $image->getSize();

        $this->assertEquals(400, $size->getWidth());
        $this->assertEquals(300, $size->getHeight());

        unlink('tests/Imagine/Fixtures/blank.png');
    }

    public function testCreateTransparentGradient()
    {
        $factory = $this->getImagine();

        $image = $factory->open('tests/Imagine/Fixtures/google.png');
        $image->copy()
            ->applyMask(new Horizontal($image->getSize()->getWidth()))
            ->save('tests/Imagine/Fixtures/transparent.png');

        $image = $factory->open('tests/Imagine/Fixtures/transparent.png');
        $size  = $image->getSize();

        $this->assertEquals(364, $size->getWidth());
        $this->assertEquals(126, $size->getHeight());

        unlink('tests/Imagine/Fixtures/transparent.png');

        $size = new Box(100, 50);
        $image = $factory->create($size, new Color('f00'));
        $image->paste(
            $factory->create($size, new Color('ff0'))
                ->applyMask(new Horizontal(100)),
            new Point(0, 0)
            )
        ->save('tests/Imagine/Fixtures/color.png');

        $image = $factory->open('tests/Imagine/Fixtures/color.png');
        $size  = $image->getSize();

        $this->assertEquals(100, $size->getWidth());
        $this->assertEquals(50, $size->getHeight());

        unlink('tests/Imagine/Fixtures/color.png');
    }

    abstract protected function getImagine();
}
