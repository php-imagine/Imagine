<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Test\Image;

use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\Point;
use Imagine\Image\Color;
use Imagine\Image\Fill\Gradient\Horizontal;
use Imagine\Image\Point\Center;
use Imagine\Test\ImagineTestCase;

abstract class AbstractImageTest extends ImagineTestCase
{
    public function testRotateWithNoBackgroundColor()
    {
        $factory = $this->getImagine();

        $image = $factory->open('tests/Imagine/Fixtures/google.png');
        $image->rotate(90);

        $size = $image->getSize();

        $this->assertSame(126, $size->getWidth());
        $this->assertSame(364, $size->getHeight());
    }

    public function testCopyResizedImageToImage()
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

    /**
     * @dataProvider provideFilters
     */
    public function testResizeWithVariousFilters($filter)
    {
        $factory = $this->getImagine();
        $image = $factory->open('tests/Imagine/Fixtures/google.png');

        $image->resize(new Box(30, 30), $filter);
    }

    public function testResizeWithInvalidFilter()
    {
        $factory = $this->getImagine();
        $image = $factory->open('tests/Imagine/Fixtures/google.png');

        $this->setExpectedException('Imagine\Exception\InvalidArgumentException');
        $image->resize(new Box(30, 30), 'no filter');
    }

    public function provideFilters()
    {
        return array(
            array(ImageInterface::FILTER_UNDEFINED),
            array(ImageInterface::FILTER_POINT),
            array(ImageInterface::FILTER_BOX),
            array(ImageInterface::FILTER_TRIANGLE),
            array(ImageInterface::FILTER_HERMITE),
            array(ImageInterface::FILTER_HANNING),
            array(ImageInterface::FILTER_HAMMING),
            array(ImageInterface::FILTER_BLACKMAN),
            array(ImageInterface::FILTER_GAUSSIAN),
            array(ImageInterface::FILTER_QUADRATIC),
            array(ImageInterface::FILTER_CUBIC),
            array(ImageInterface::FILTER_CATROM),
            array(ImageInterface::FILTER_MITCHELL),
            array(ImageInterface::FILTER_LANCZOS),
            array(ImageInterface::FILTER_BESSEL),
            array(ImageInterface::FILTER_SINC),
        );
    }

    public function testThumbnailShouldReturnACopy()
    {
        $factory = $this->getImagine();

        $image = $factory->open('tests/Imagine/Fixtures/google.png');
        $thumbnail = $image->thumbnail(new Box(20, 20));

        $this->assertNotSame($image, $thumbnail);
    }

    public function testResizeShouldReturnTheImage()
    {
        $factory = $this->getImagine();

        $image = $factory->open('tests/Imagine/Fixtures/google.png');
        $resized = $image->resize(new Box(20, 20));

        $this->assertSame($image, $resized);
    }

    /**
     * @dataProvider provideDimensionsAndModesForThumbnailGeneration
     */
    public function testThumbnailGeneration($sourceW, $sourceH, $thumbW, $thumbH, $mode, $expectedW, $expectedH)
    {
        $factory = $this->getImagine();
        $image   = $factory->create(new Box($sourceW, $sourceH));
        $inset   = $image->thumbnail(new Box($thumbW, $thumbH), $mode);

        $size = $inset->getSize();

        $this->assertEquals($expectedW, $size->getWidth());
        $this->assertEquals($expectedH, $size->getHeight());
    }

    public function provideDimensionsAndModesForThumbnailGeneration()
    {
        return array(
            // landscape with smaller portrait
            array(320, 240, 32, 48, ImageInterface::THUMBNAIL_INSET, 32, round(32 * 240 / 320)),
            array(320, 240, 32, 48, ImageInterface::THUMBNAIL_OUTBOUND, 32, 48),
            // landscape with smaller landscape
            array(320, 240, 32, 16, ImageInterface::THUMBNAIL_INSET, round(16 * 320 / 240), 16),
            array(320, 240, 32, 16, ImageInterface::THUMBNAIL_OUTBOUND, 32, 16),

            // portait with smaller portrait
            array(240, 320, 24, 48, ImageInterface::THUMBNAIL_INSET, 24, round(24 * 320 / 240)),
            array(240, 320, 24, 48, ImageInterface::THUMBNAIL_OUTBOUND, 24, 48),
            // portait with smaller landscape
            array(240, 320, 24, 16, ImageInterface::THUMBNAIL_INSET, round(16 * 240 / 320), 16),
            array(240, 320, 24, 16, ImageInterface::THUMBNAIL_OUTBOUND, 24, 16),

            // landscape with larger portrait
            array(32, 24, 320, 300, ImageInterface::THUMBNAIL_INSET, 32, 24),
            array(32, 24, 320, 300, ImageInterface::THUMBNAIL_OUTBOUND, 32, 24),
            // landscape with larger landscape
            array(32, 24, 320, 200, ImageInterface::THUMBNAIL_INSET, 32, 24),
            array(32, 24, 320, 200, ImageInterface::THUMBNAIL_OUTBOUND, 32, 24),

            // portait with larger portrait
            array(24, 32, 240, 300, ImageInterface::THUMBNAIL_INSET, 24, 32),
            array(24, 32, 240, 300, ImageInterface::THUMBNAIL_OUTBOUND, 24, 32),
            // portait with larger landscape
            array(24, 32, 240, 400, ImageInterface::THUMBNAIL_INSET, 24, 32),
            array(24, 32, 240, 400, ImageInterface::THUMBNAIL_OUTBOUND, 24, 32),

            // landscape with intersect portrait
            array(320, 240, 340, 220, ImageInterface::THUMBNAIL_INSET, round(220 * 320 / 240), 220),
            array(320, 240, 340, 220, ImageInterface::THUMBNAIL_OUTBOUND, 320, 220),
            // landscape with intersect portrait
            array(320, 240, 300, 360, ImageInterface::THUMBNAIL_INSET, 300, round(300 / 320 * 240)),
            array(320, 240, 300, 360, ImageInterface::THUMBNAIL_OUTBOUND, 300, 240),
        );
    }

    public function testThumbnailGenerationToDimensionsLergestThanSource()
    {
        $test_image = 'tests/Imagine/Fixtures/google.png';
        $test_image_width = 364;
        $test_image_height = 126;
        $width = $test_image_width + 1;
        $height = $test_image_height + 1;

        $factory = $this->getImagine();
        $image   = $factory->open($test_image);
        $size = $image->getSize();

        $this->assertEquals($test_image_width, $size->getWidth());
        $this->assertEquals($test_image_height, $size->getHeight());

        $inset   = $image->thumbnail(new Box($width, $height), ImageInterface::THUMBNAIL_INSET);
        $size = $inset->getSize();
        unset($inset);

        $this->assertEquals($test_image_width, $size->getWidth());
        $this->assertEquals($test_image_height, $size->getHeight());

        $outbound = $image->thumbnail(new Box($width, $height), ImageInterface::THUMBNAIL_OUTBOUND);
        $size = $outbound->getSize();
        unset($outbound);
        unset($image);

        $this->assertEquals($test_image_width, $size->getWidth());
        $this->assertEquals($test_image_height, $size->getHeight());
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

    public function testImageResolutionChange()
    {
        $imagine = $this->getImagine();
        $image = $imagine->open('tests/Imagine/Fixtures/resize/210-design-19933.jpg');
        $outfile = 'tests/Imagine/Fixtures/resize/reduced.jpg';
        $image->save($outfile, array(
            'resolution-units' => ImageInterface::RESOLUTION_PIXELSPERINCH,
            'resolution-x' => 144,
            'resolution-y' => 144
        ));

        if ($imagine instanceof \Imagine\Imagick\Imagine) {
            $i = new \Imagick($outfile);
            $info = $i->identifyimage();
            $this->assertEquals(144, $info['resolution']['x']);
            $this->assertEquals(144, $info['resolution']['y']);
        }
        if ($imagine instanceof \Imagine\Gmagick\Imagine) {
            $i = new \Gmagick($outfile);
            $info = $i->getimageresolution();
            $this->assertEquals(144, $info['x']);
            $this->assertEquals(144, $info['y']);
        }

        unlink($outfile);
    }

    public function testInOutResult()
    {
        $this->processInOut("trans", "png","png");
        $this->processInOut("trans", "png","gif");
        $this->processInOut("trans", "png","jpg");
        $this->processInOut("anima", "gif","png");
        $this->processInOut("anima", "gif","gif");
        $this->processInOut("anima", "gif","jpg");
        $this->processInOut("trans", "gif","png");
        $this->processInOut("trans", "gif","gif");
        $this->processInOut("trans", "gif","jpg");
        $this->processInOut("large", "jpg","png");
        $this->processInOut("large", "jpg","gif");
        $this->processInOut("large", "jpg","jpg");
    }

    public function testLayerReturnsALayerInterface()
    {
        $factory = $this->getImagine();

        $image = $factory->open('tests/Imagine/Fixtures/google.png');

        $this->assertInstanceOf('Imagine\\Image\\LayersInterface', $image->layers());
    }

    public function testCountAMonoLayeredImage()
    {
        $this->assertEquals(1, count($this->getMonoLayeredImage()->layers()));
    }

    public function testCountAMultiLayeredImage()
    {
        if (!$this->supportMultipleLayers()) {
            $this->markTestSkipped('This driver does not support multiple layers');
        }

        $this->assertGreaterThan(1, count($this->getMultiLayeredImage()->layers()));
    }

    public function testLayerOnMonoLayeredImage()
    {
        foreach ($this->getMonoLayeredImage()->layers() as $layer) {
            $this->assertInstanceOf('Imagine\\Image\\ImageInterface', $layer);
            $this->assertCount(1, $layer->layers());
        }
    }

    public function testLayerOnMultiLayeredImage()
    {
        foreach ($this->getMultiLayeredImage()->layers()  as $layer) {
            $this->assertInstanceOf('Imagine\\Image\\ImageInterface', $layer);
            $this->assertCount(1, $layer->layers());
        }
    }

    private function getMonoLayeredImage()
    {
        return $this->getImagine()->open('tests/Imagine/Fixtures/google.png');
    }

    private function getMultiLayeredImage()
    {
        return $this->getImagine()->open('tests/Imagine/Fixtures/cat.gif');
    }

    private function getInconsistentMultiLayeredImage()
    {
        return $this->getImagine()->open('tests/Imagine/Fixtures/anima.gif');
    }

    protected function processInOut($file, $in, $out)
    {
        $factory = $this->getImagine();
        $class = preg_replace('/\\\\/', "_", get_called_class());
        $image = $factory->open('tests/Imagine/Fixtures/'.$file.'.'.$in);
        $thumb = $image->thumbnail(new Box(50, 50), ImageInterface::THUMBNAIL_OUTBOUND);
        $thumb->save("tests/Imagine/Fixtures/results/in_out/{$class}_{$file}_from_{$in}_to.{$out}");

    }

    abstract protected function getImagine();
    abstract protected function supportMultipleLayers();
}
