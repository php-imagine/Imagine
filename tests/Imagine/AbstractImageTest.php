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

use Imagine\Fill\Gradient\Horizontal;
use Imagine\Fill\Gradient\Vertical;
use Imagine\Image\Box;
use Imagine\Image\Color;
use Imagine\Image\Point;
use Imagine\Image\Point\Center;
use Imagine\AbstractLibraryDependentTest;

abstract class AbstractImageTest extends AbstractLibraryDependentTest
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
            );
    }

    public function testThumbnailGeneration()
    {
        $factory = $this->getImagine();
        $image   = $factory->open('tests/Imagine/Fixtures/google.png');
        $inset   = $image->thumbnail(new Box(50, 50), ImageInterface::THUMBNAIL_INSET);

        $size = $inset->getSize();

        unset($inset);

        $this->assertEquals(50, $size->getWidth());
        $this->assertEquals(17, $size->getHeight());

        $outbound = $image->thumbnail(new Box(50, 50), ImageInterface::THUMBNAIL_OUTBOUND);

        $size = $outbound->getSize();

        unset($outbound);
        unset($image);

        $this->assertEquals(50, $size->getWidth());
        $this->assertEquals(50, $size->getHeight());
    }

    public function testCropResizeFlip()
    {
        $factory = $this->getImagine();

        $image = $factory->open('tests/Imagine/Fixtures/google.png')
            ->crop(new Point(0, 0), new Box(126, 126))
            ->resize(new Box(200, 200))
            ->flipHorizontally();

        $size = $image->getSize();

        unset($image);

        $this->assertEquals(200, $size->getWidth());
        $this->assertEquals(200, $size->getHeight());
    }

    public function testCreateAndSaveEmptyImage()
    {
        $factory = $this->getImagine();
        $image   = $factory->create(new Box(400, 300), new Color('000'));

        $size  = $image->getSize();

        unset($image);

        $this->assertEquals(400, $size->getWidth());
        $this->assertEquals(300, $size->getHeight());
    }

    public function testCreateTransparentGradient()
    {
        $factory = $this->getImagine();
        $size    = new Box(100, 50);
        $image   = $factory->create($size, new Color('f00'));

        $image->paste(
                $factory->create($size, new Color('ff0'))
                    ->applyMask(
                        $factory->create($size)
                            ->fill(
                                new Horizontal(
                                    $image->getSize()->getWidth(),
                                    new Color('fff'),
                                    new Color('000')
                                )
                            )
                    ),
                new Point(0, 0)
            );

        $size = $image->getSize();

        unset($image);

        $this->assertEquals(100, $size->getWidth());
        $this->assertEquals(50, $size->getHeight());
    }

    public function testMask()
    {
        $factory = $this->getImagine();

        $image = $factory->open('tests/Imagine/Fixtures/google.png');

        $image->applyMask($image->mask())
            ->save('tests/Imagine/Fixtures/mask.png');

        $size = $factory->open('tests/Imagine/Fixtures/mask.png')
            ->getSize();

        $this->assertEquals(364, $size->getWidth());
        $this->assertEquals(126, $size->getHeight());

        unlink('tests/Imagine/Fixtures/mask.png');
    }

    public function testColorHistogram()
    {
        $factory = $this->getImagine();

        $image = $factory->open('tests/Imagine/Fixtures/google.png');

        $this->assertEquals(6438, count($image->histogram()));
    }

    abstract protected function getImagine();
}
