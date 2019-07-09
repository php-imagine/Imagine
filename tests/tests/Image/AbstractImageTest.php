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
use Imagine\Image\Fill\Gradient\Horizontal;
use Imagine\Image\ImageInterface;
use Imagine\Image\Palette\RGB;
use Imagine\Image\Point;
use Imagine\Image\Point\Center;
use Imagine\Image\PointSigned;
use Imagine\Image\Profile;
use Imagine\Test\ImagineTestCase;

abstract class AbstractImageTest extends ImagineTestCase
{
    public function testPaletteIsRGBIfRGBImage()
    {
        $image = $this->getImagine()->open(IMAGINE_TEST_FIXTURESFOLDER . '/google.png');
        $this->assertInstanceOf('Imagine\Image\Palette\RGB', $image->palette());
    }

    public function testPaletteIsCMYKIfCMYKImage()
    {
        $image = $this->getImagine()->open(IMAGINE_TEST_FIXTURESFOLDER . '/pixel-CMYK.jpg');
        $this->assertInstanceOf('Imagine\Image\Palette\CMYK', $image->palette());
    }

    public function testPaletteIsGrayIfGrayImage()
    {
        $image = $this->getImagine()->open(IMAGINE_TEST_FIXTURESFOLDER . '/pixel-grayscale.jpg');
        $this->assertInstanceOf('Imagine\Image\Palette\Grayscale', $image->palette());
    }

    public function testDefaultPaletteCreationIsRGB()
    {
        $image = $this->getImagine()->create(new Box(10, 10));
        $this->assertInstanceOf('Imagine\Image\Palette\RGB', $image->palette());
    }

    /**
     * @dataProvider providePalettes
     *
     * @param mixed $paletteClass
     * @param mixed $input
     */
    public function testPaletteAssociatedIsRelatedToGivenColor($paletteClass, $input)
    {
        $palette = new $paletteClass();
        /* @var \Imagine\Image\Palette\PaletteInterface $palette */
        $palette->profile();

        $image = $this
            ->getImagine()
            ->create(new Box(10, 10), $palette->color($input));

        $image->palette()->profile();
        $this->assertEquals($palette, $image->palette());
    }

    public function providePalettes()
    {
        return array(
            array('Imagine\Image\Palette\RGB', array(255, 0, 0)),
            array('Imagine\Image\Palette\CMYK', array(10, 0, 0, 0)),
            array('Imagine\Image\Palette\Grayscale', array(25)),
        );
    }

    /**
     * @dataProvider provideFromAndToPalettes
     *
     * @param string $from
     * @param string $to
     * @param int[] $color
     */
    public function testUsePalette($from, $to, $color)
    {
        $palette = new $from();

        $image = $this
            ->getImagine()
            ->create(new Box(10, 10), $palette->color($color));

        $targetPalette = new $to();

        $image->usePalette($targetPalette);

        $this->assertEquals($targetPalette, $image->palette());
        $suffix = array();
        $tmp = explode('\\', $from);
        $suffix[] = array_pop($tmp);
        $tmp = explode('\\', $to);
        $suffix[] = array_pop($tmp);
        $suffix[] = implode('-', $color);
        $filename = $this->getTemporaryFilename(implode('-', $suffix) . '.jpg');
        $image->save($filename);

        $image = $this->getImagine()->open($filename);

        $this->assertInstanceOf($to, $image->palette());
    }

    public function testSaveWithoutFormatShouldSaveInOriginalFormat()
    {
        if (!extension_loaded('exif')) {
            $this->markTestSkipped('The EXIF extension is required for this test');
        }

        $tmpFile = $this->getTemporaryFilename('');

        $this
            ->getImagine()
            ->open(IMAGINE_TEST_FIXTURESFOLDER . '/large.jpg')
            ->save($tmpFile);

        $data = exif_read_data($tmpFile);
        $this->assertEquals('image/jpeg', $data['MimeType']);
    }

    public function testSaveWithoutPathFileFromImageLoadShouldBeOkay()
    {
        $source = IMAGINE_TEST_FIXTURESFOLDER . '/google.png';
        $tmpFile = $this->getTemporaryFilename('.png');

        copy($source, $tmpFile);

        $this->assertEquals(md5_file($source), md5_file($tmpFile));

        $this
            ->getImagine()
            ->open($tmpFile)
            ->resize(new Box(20, 20))
            ->save();

        $this->assertNotEquals(md5_file($source), md5_file($tmpFile));
    }

    public function testSaveWithoutPathFileFromImageCreationShouldFail()
    {
        $this->isGoingToThrowException('Imagine\Exception\RuntimeException');
        $image = $this->getImagine()->create(new Box(20, 20));
        $image->save();
    }

    public function provideFromAndToPalettes()
    {
        return array(
            array(
                'Imagine\Image\Palette\RGB',
                'Imagine\Image\Palette\CMYK',
                array(10, 10, 10),
            ),
            array(
                'Imagine\Image\Palette\RGB',
                'Imagine\Image\Palette\Grayscale',
                array(10, 10, 10),
            ),
            array(
                'Imagine\Image\Palette\CMYK',
                'Imagine\Image\Palette\RGB',
                array(10, 10, 10, 0),
            ),
            array(
                'Imagine\Image\Palette\CMYK',
                'Imagine\Image\Palette\Grayscale',
                array(10, 10, 10, 0),
            ),
            array(
                'Imagine\Image\Palette\Grayscale',
                'Imagine\Image\Palette\RGB',
                array(10),
            ),
            array(
                'Imagine\Image\Palette\Grayscale',
                'Imagine\Image\Palette\CMYK',
                array(10),
            ),
        );
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testProfile()
    {
        $this
            ->getImagine()
            ->create(new Box(10, 10))
            ->profile(Profile::fromPath(IMAGINE_TEST_SRCFOLDER . '/resources/Adobe/RGB/VideoHD.icc'));
    }

    public function testRotateWithNoBackgroundColor()
    {
        $factory = $this->getImagine();

        $image = $factory->open(IMAGINE_TEST_FIXTURESFOLDER . '/google.png');
        $image->rotate(90);

        $size = $image->getSize();

        $this->assertSame(126, $size->getWidth());
        $this->assertSame(364, $size->getHeight());
    }

    public function testRotateWithTransparency()
    {
        $image = $this->getImagine()->open(IMAGINE_TEST_FIXTURESFOLDER . '/large.jpg');
        $color = $image->rotate(45, $image->palette()->color('#fff', 0))->getColorAt(new Point(0, 0));
        $this->assertSame(0, $color->getAlpha());
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testCopyResizedImageToImage()
    {
        $factory = $this->getImagine();

        $image = $factory->open(IMAGINE_TEST_FIXTURESFOLDER . '/google.png');
        $size = $image->getSize();

        $image->paste(
                $image->copy()
                    ->resize($size->scale(0.5))
                    ->flipVertically(),
                new Center($size)
            );
    }

    /**
     * @dataProvider provideFilters
     *
     * @param mixed $filter
     *
     * @doesNotPerformAssertions
     */
    public function testResizeWithVariousFilters($filter)
    {
        $factory = $this->getImagine();
        $image = $factory->open(IMAGINE_TEST_FIXTURESFOLDER . '/google.png');

        $image->resize(new Box(30, 30), $filter);
    }

    public function testResizeWithInvalidFilter()
    {
        $factory = $this->getImagine();
        $image = $factory->open(IMAGINE_TEST_FIXTURESFOLDER . '/google.png');

        $this->isGoingToThrowException('Imagine\Exception\InvalidArgumentException');
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

        $image = $factory->open(IMAGINE_TEST_FIXTURESFOLDER . '/google.png');
        $thumbnail = $image->thumbnail(new Box(20, 20));

        $this->assertNotSame($image, $thumbnail);

        $thumbnail = $image->thumbnail(new Box(20, 20), ImageInterface::THUMBNAIL_INSET | ImageInterface::THUMBNAIL_FLAG_NOCLONE);
        $this->assertSame($image, $thumbnail);
    }

    public function testThumbnailWithInvalidSettingShouldThrowAnException()
    {
        $factory = $this->getImagine();
        $image = $factory->open(IMAGINE_TEST_FIXTURESFOLDER . '/google.png');
        $this->isGoingToThrowException('Imagine\Exception\InvalidArgumentException', 'Invalid setting specified');
        $image->thumbnail(new Box(20, 20), 'boumboum');
    }

    public function testThumbnailWithInvalidModeShouldThrowAnException()
    {
        $factory = $this->getImagine();
        $image = $factory->open(IMAGINE_TEST_FIXTURESFOLDER . '/google.png');
        $this->isGoingToThrowException('Imagine\Exception\InvalidArgumentException', 'Only one mode should be specified');
        $image->thumbnail(new Box(20, 20), ImageInterface::THUMBNAIL_INSET | ImageInterface::THUMBNAIL_OUTBOUND);
    }

    public function testResizeShouldReturnTheImage()
    {
        $factory = $this->getImagine();

        $image = $factory->open(IMAGINE_TEST_FIXTURESFOLDER . '/google.png');
        $resized = $image->resize(new Box(20, 20));

        $this->assertSame($image, $resized);
    }

    /**
     * @dataProvider provideDimensionsAndModesForThumbnailGeneration
     *
     * @param mixed $sourceW
     * @param mixed $sourceH
     * @param mixed $thumbW
     * @param mixed $thumbH
     * @param mixed $settings
     * @param mixed $expectedW
     * @param mixed $expectedH
     */
    public function testThumbnailGeneration($sourceW, $sourceH, $thumbW, $thumbH, $settings, $expectedW, $expectedH)
    {
        $factory = $this->getImagine();
        $image = $factory->create(new Box($sourceW, $sourceH));
        $thumb = $image->thumbnail(new Box($thumbW, $thumbH), $settings);
        $size = $thumb->getSize();

        $this->assertEquals($expectedW, $size->getWidth());
        $this->assertEquals($expectedH, $size->getHeight());
    }

    public function provideDimensionsAndModesForThumbnailGeneration()
    {
        return array(
            // support previous values of setting constants
            array(320, 240, 32, 48, 'inset', 32, round(32 * 240 / 320)),
            array(320, 240, 32, 48, 'outbound', 32, 48),

            // landscape with smaller portrait
            array(320, 240, 32, 48, ImageInterface::THUMBNAIL_INSET, 32, round(32 * 240 / 320)),
            array(320, 240, 32, 48, ImageInterface::THUMBNAIL_OUTBOUND, 32, 48),
            // landscape with smaller landscape
            array(320, 240, 32, 16, ImageInterface::THUMBNAIL_INSET, round(16 * 320 / 240), 16),
            array(320, 240, 32, 16, ImageInterface::THUMBNAIL_OUTBOUND, 32, 16),

            // portrait with smaller portrait
            array(240, 320, 24, 48, ImageInterface::THUMBNAIL_INSET, 24, round(24 * 320 / 240)),
            array(240, 320, 24, 48, ImageInterface::THUMBNAIL_OUTBOUND, 24, 48),
            // portrait with smaller landscape
            array(240, 320, 24, 16, ImageInterface::THUMBNAIL_INSET, round(16 * 240 / 320), 16),
            array(240, 320, 24, 16, ImageInterface::THUMBNAIL_OUTBOUND, 24, 16),

            // landscape with larger portrait
            array(32, 24, 320, 300, ImageInterface::THUMBNAIL_INSET, 32, 24),
            array(32, 24, 320, 300, ImageInterface::THUMBNAIL_OUTBOUND, 32, 24),
            // landscape with larger landscape
            array(32, 24, 320, 200, ImageInterface::THUMBNAIL_INSET, 32, 24),
            array(32, 24, 320, 200, ImageInterface::THUMBNAIL_OUTBOUND, 32, 24),

            // portrait with larger portrait
            array(24, 32, 240, 300, ImageInterface::THUMBNAIL_INSET, 24, 32),
            array(24, 32, 240, 300, ImageInterface::THUMBNAIL_OUTBOUND, 24, 32),
            // portrait with larger landscape
            array(24, 32, 240, 400, ImageInterface::THUMBNAIL_INSET, 24, 32),
            array(24, 32, 240, 400, ImageInterface::THUMBNAIL_OUTBOUND, 24, 32),

            // landscape with larger portrait (allow upscale)
            array(32, 24, 320, 300, ImageInterface::THUMBNAIL_INSET | ImageInterface::THUMBNAIL_FLAG_UPSCALE, 320, round(24 * 320 / 32)),
            array(32, 24, 320, 300, ImageInterface::THUMBNAIL_OUTBOUND | ImageInterface::THUMBNAIL_FLAG_UPSCALE, 320, 300),
            // landscape with larger landscape (allow upscale)
            array(32, 24, 320, 200, ImageInterface::THUMBNAIL_INSET | ImageInterface::THUMBNAIL_FLAG_UPSCALE, round(32 * 200 / 24), 200),
            array(32, 24, 320, 200, ImageInterface::THUMBNAIL_OUTBOUND | ImageInterface::THUMBNAIL_FLAG_UPSCALE, 320, 200),

            // portrait with larger portrait (allow upscale)
            array(24, 32, 240, 300, ImageInterface::THUMBNAIL_INSET | ImageInterface::THUMBNAIL_FLAG_UPSCALE, round(24 * 300 / 32), 300),
            array(24, 32, 240, 300, ImageInterface::THUMBNAIL_OUTBOUND | ImageInterface::THUMBNAIL_FLAG_UPSCALE, 240, 300),
            // portrait with larger landscape (allow upscale)
            array(24, 32, 240, 400, ImageInterface::THUMBNAIL_INSET | ImageInterface::THUMBNAIL_FLAG_UPSCALE, 240, round(32 * 240 / 24)),
            array(24, 32, 240, 400, ImageInterface::THUMBNAIL_OUTBOUND | ImageInterface::THUMBNAIL_FLAG_UPSCALE, 240, 400),

            // landscape with intersect portrait
            array(320, 240, 340, 220, ImageInterface::THUMBNAIL_INSET, round(220 * 320 / 240), 220),
            array(320, 240, 340, 220, ImageInterface::THUMBNAIL_OUTBOUND, 320, 220),
            // landscape with intersect portrait
            array(320, 240, 300, 360, ImageInterface::THUMBNAIL_INSET, 300, round(300 / 320 * 240)),
            array(320, 240, 300, 360, ImageInterface::THUMBNAIL_OUTBOUND, 300, 240),
        );
    }

    public function testThumbnailGenerationToDimensionsLargestThanSource()
    {
        $test_image = IMAGINE_TEST_FIXTURESFOLDER . '/google.png';
        $test_image_width = 364;
        $test_image_height = 126;
        $width = $test_image_width + 1;
        $height = $test_image_height + 1;

        $factory = $this->getImagine();
        $image = $factory->open($test_image);
        $size = $image->getSize();

        $this->assertEquals($test_image_width, $size->getWidth());
        $this->assertEquals($test_image_height, $size->getHeight());

        $inset = $image->thumbnail(new Box($width, $height), ImageInterface::THUMBNAIL_INSET);
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

        $image = $factory->open(IMAGINE_TEST_FIXTURESFOLDER . '/google.png')
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

        $palette = new RGB();

        $image = $factory->create(new Box(400, 300), $palette->color('000'));

        $size = $image->getSize();

        unset($image);

        $this->assertEquals(400, $size->getWidth());
        $this->assertEquals(300, $size->getHeight());
    }

    public function testCreateTransparentGradient()
    {
        $factory = $this->getImagine();

        $palette = new RGB();

        $size = new Box(100, 50);
        $image = $factory->create($size, $palette->color('f00'));

        $image->paste(
                $factory->create($size, $palette->color('ff0'))
                    ->applyMask(
                        $factory->create($size)
                            ->fill(
                                new Horizontal(
                                    $image->getSize()->getWidth(),
                                    $palette->color('fff'),
                                    $palette->color('000')
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

        $image = $factory->open(IMAGINE_TEST_FIXTURESFOLDER . '/google.png');

        $filename = $this->getTemporaryFilename('.png');
        $image->applyMask($image->mask())
            ->save($filename);

        $size = $factory->open($filename)
            ->getSize();

        $this->assertEquals(364, $size->getWidth());
        $this->assertEquals(126, $size->getHeight());
    }

    public function testColorHistogram()
    {
        $factory = $this->getImagine();

        $image = $factory->open(IMAGINE_TEST_FIXTURESFOLDER . '/google.png');

        $this->assertEquals(6438, count($image->histogram()));
    }

    public function testImageResolutionChange()
    {
        $imagine = $this->getImagine();
        $image = $imagine->open(IMAGINE_TEST_FIXTURESFOLDER . '/resize/210-design-19933.jpg');
        $outfile = $this->getTemporaryFilename('.jpg');
        $image->save($outfile, array(
            'resolution-units' => ImageInterface::RESOLUTION_PIXELSPERINCH,
            'resolution-x' => 144,
            'resolution-y' => 144,
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
    }

    public function inOutResultProvider()
    {
        return array(
            array('trans', 'png', 'png'),
            array('trans', 'png', 'gif'),
            array('trans', 'png', 'jpg'),
            array('anima', 'gif', 'png'),
            array('anima', 'gif', 'gif'),
            array('anima', 'gif', 'jpg'),
            array('trans', 'gif', 'png'),
            array('trans', 'gif', 'gif'),
            array('trans', 'gif', 'jpg'),
            array('large', 'jpg', 'png'),
            array('large', 'jpg', 'gif'),
            array('large', 'jpg', 'jpg'),
        );
    }

    /**
     * @dataProvider inOutResultProvider
     *
     * @param string $file
     * @param string $in
     * @param string $out
     *
     * @doesNotPerformAssertions
     */
    public function testInOutResult($file, $in, $out)
    {
        $factory = $this->getImagine();
        $image = $factory->open(IMAGINE_TEST_FIXTURESFOLDER . "/{$file}.{$in}");
        $thumb = $image->thumbnail(new Box(50, 50), ImageInterface::THUMBNAIL_OUTBOUND);
        $filename = $this->getTemporaryFilename("{$file}-{$in}.{$out}");
        $thumb->save($filename);
    }

    public function testLayerReturnsALayerInterface()
    {
        $factory = $this->getImagine();

        $image = $factory->open(IMAGINE_TEST_FIXTURESFOLDER . '/google.png');

        $this->assertInstanceOf('Imagine\\Image\\LayersInterface', $image->layers());
    }

    public function testCountAMonoLayeredImage()
    {
        $this->assertEquals(1, count($this->getMonoLayeredImage()->layers()));
    }

    public function testCountAMultiLayeredImage()
    {
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
        foreach ($this->getMultiLayeredImage()->layers() as $layer) {
            $this->assertInstanceOf('Imagine\\Image\\ImageInterface', $layer);
            $this->assertCount(1, $layer->layers());
        }
    }

    public function testChangeColorSpaceAndStripImage()
    {
        $palette = new RGB();
        $color = $this
            ->getImagine()
            ->open(IMAGINE_TEST_FIXTURESFOLDER . '/pixel-CMYK.jpg')
            ->usePalette($palette)
            ->strip()
            ->getColorAt(new Point(0, 0));

        $this->assertColorSimilar($palette->color('#0082a2'), $color, '', 1.4143, false);
    }

    public function testStripImageWithInvalidProfile()
    {
        $image = $this
            ->getImagine()
            ->open(IMAGINE_TEST_FIXTURESFOLDER . '/invalid-icc-profile.jpg');

        $color = $image->getColorAt(new Point(0, 0));
        $image->strip();
        $afterColor = $image->getColorAt(new Point(0, 0));

        $this->assertEquals((string) $color, (string) $afterColor);
    }

    public function testGetColorAt()
    {
        $color = $this
            ->getImagine()
            ->open(IMAGINE_TEST_FIXTURESFOLDER . '/65-percent-black.png')
            ->getColorAt(new Point(0, 0));

        $this->assertEquals('#000000', (string) $color);
        $this->assertFalse($color->isOpaque());
        $this->assertEquals('65', $color->getAlpha());
    }

    public function testGetColorAtGrayScale()
    {
        $color = $this
            ->getImagine()
            ->open(IMAGINE_TEST_FIXTURESFOLDER . '/pixel-grayscale.jpg')
            ->getColorAt(new Point(0, 0));

        $this->assertEquals('#4d4d4d', (string) $color);
        $this->assertTrue($color->isOpaque());
    }

    public function testGetColorAtCMYK()
    {
        $color = $this
            ->getImagine()
            ->open(IMAGINE_TEST_FIXTURESFOLDER . '/pixel-CMYK.jpg')
            ->getColorAt(new Point(0, 0));

        $this->assertEquals('cmyk(99%, 0%, 31%, 23%)', (string) $color);
        $this->assertTrue($color->isOpaque());
    }

    public function testGetColorAtOpaque()
    {
        $color = $this
            ->getImagine()
            ->open(IMAGINE_TEST_FIXTURESFOLDER . '/100-percent-black.png')
            ->getColorAt(new Point(0, 0));

        $this->assertEquals('#000000', (string) $color);
        $this->assertTrue($color->isOpaque());

        $this->assertSame(0, $color->getRed());
        $this->assertSame(0, $color->getGreen());
        $this->assertSame(0, $color->getBlue());
    }

    public function testStripGBRImageHasGoodColors()
    {
        $image = $this
            ->getImagine()
            ->open(IMAGINE_TEST_FIXTURESFOLDER . '/pixel-GBR.jpg')
            ->strip();
        $color = $image->getColorAt(new Point(0, 0));

        $this->assertColorSimilar($image->palette()->color('#d07560'), $color, '', 1);
    }

    /**
     * Test whether a simple action such as resizing a GIF works
     * Using the original animated GIF and a slightly more complex one as reference
     * anima2.gif courtesy of Cyndi Norrie (http://cyndipop.tumblr.com/) via 15 Folds (http://15folds.com).
     *
     * @doesNotPerformAssertions
     */
    public function testResizeAnimatedGifResizeResult()
    {
        $imagine = $this->getImagine();

        $image = $imagine->open(IMAGINE_TEST_FIXTURESFOLDER . '/anima.gif');

        // Imagick requires the images to be coalesced first!
        if ($image instanceof \Imagine\Imagick\Image) {
            $image->layers()->coalesce();
        }

        foreach ($image->layers() as $frame) {
            $frame->resize(new Box(121, 124));
        }

        $filename = $this->getTemporaryFilename('anima.gif');
        $image->save($filename, array('animated' => true));

        $image = $imagine->open(IMAGINE_TEST_FIXTURESFOLDER . '/anima2.gif');

        // Imagick requires the images to be coalesced first!
        if ($image instanceof \Imagine\Imagick\Image) {
            $image->layers()->coalesce();
        }

        foreach ($image->layers() as $frame) {
            $frame->resize(new Box(200, 144));
        }

        $filename = $this->getTemporaryFilename('anima2.gif');
        $image->save($filename, array('animated' => true));
    }

    public function testMetadataReturnsMetadataInstance()
    {
        $this->assertInstanceOf('Imagine\Image\Metadata\MetadataBag', $this->getMonoLayeredImage()->metadata());
    }

    public function cloneWorksProvider()
    {
        return array(
            array(true),
            array(false),
        );
    }

    /**
     * @dataProvider cloneWorksProvider
     *
     * @param mixed $withCopy
     */
    public function testCloneWorks($withCopy)
    {
        $size = new Box(5, 10);
        $image = $this->getImagine()->create($size);
        $palette = $image->palette();
        $metadata = $image->metadata();
        $layers = $image->layers();
        if ($withCopy) {
            $clone = $image->copy();
        } else {
            $clone = clone $image;
        }
        $this->assertEquals($size, $clone->getSize());
        $this->assertNotSame($palette, $clone->palette());
        $this->assertNotSame($metadata, $clone->metadata());
        $this->assertNotSame($layers, $clone->layers());
        $this->assertEquals($layers->key(), $clone->layers()->key());
        unset($clone);
        $this->assertEquals($size, $image->getSize());
        $this->assertSame($palette, $image->palette());
        $this->assertSame($metadata, $image->metadata());
        $this->assertSame($layers, $image->layers());
    }

    public function testImageSizeOnAnimatedGif()
    {
        $imagine = $this->getImagine();

        $image = $imagine->open(IMAGINE_TEST_FIXTURESFOLDER . '/anima3.gif');

        $size = $image->getSize();

        $this->assertEquals(300, $size->getWidth());
        $this->assertEquals(200, $size->getHeight());
    }

    /**
     * @dataProvider provideVariousSources
     *
     * @param mixed $source
     */
    public function testResolutionOnSave($source)
    {
        $file = $this->getTemporaryFilename(basename($source) . '.jpg');

        $image = $this->getImagine()->open($source);
        $image->save($file, array(
            'resolution-units' => ImageInterface::RESOLUTION_PIXELSPERINCH,
            'resolution-x' => 150,
            'resolution-y' => 120,
            'resampling-filter' => ImageInterface::FILTER_LANCZOS,
        ));

        $saved = $this->getImagine()->open($file);
        $this->assertEquals(array('x' => 150, 'y' => 120), $this->getImageResolution($saved));
    }

    public function provideVariousSources()
    {
        return array(
            array(IMAGINE_TEST_FIXTURESFOLDER . '/example.svg'),
            array(IMAGINE_TEST_FIXTURESFOLDER . '/100-percent-black.png'),
        );
    }

    public function testFillAlphaPrecision()
    {
        $imagine = $this->getImagine();
        $palette = new RGB();
        $image = $imagine->create(new Box(1, 1), $palette->color('#f00'));
        $fill = new Horizontal(100, $palette->color('#f00', 17), $palette->color('#f00', 73));
        $image->fill($fill);

        $actualColor = $image->getColorAt(new Point(0, 0));
        $this->assertEquals(17, $actualColor->getAlpha());
    }

    public function testImageCreatedAlpha()
    {
        $palette = new RGB();
        $image = $this->getImagine()->create(new Box(1, 1), $palette->color('#7f7f7f', 10));
        $actualColor = $image->getColorAt(new Point(0, 0));

        $this->assertEquals('#7f7f7f', (string) $actualColor);
        $this->assertEquals(10, $actualColor->getAlpha());
    }

    /**
     * @dataProvider imageAlphaLoadedProvider
     *
     * @param mixed $path
     */
    public function testImageAlphaLoaded($path)
    {
        $image = $this->getImagine()->open(IMAGINE_TEST_FIXTURESFOLDER . '/alpha/' . $path);
        $size = $image->getSize();
        $this->assertSame('5x1', "{$size->getWidth()}x{$size->getHeight()}", 'Checking size of loaded image');
        foreach (array(
            0 => 0,
            1 => 25,
            2 => 50,
            3 => 75,
            4 => 100,
        ) as $x => $expectedAlpha) {
            $alpha = $image->getColorAt(new Point($x, 0))->getAlpha();
            $this->assertTrue(abs($alpha - $expectedAlpha) <= 2, "Checking pixel at x={$x} has an alpha near to {$expectedAlpha} (found: {$alpha})");
        }
    }

    public function imageAlphaLoadedProvider()
    {
        return array(
            array('grayscale-alpha.png'),
            array('palette.png'),
            array('truecolor-alpha.png'),
        );
    }

    public function testJpegSamplingFactors()
    {
        $image = $this->getImagine()->open(IMAGINE_TEST_FIXTURESFOLDER . '/large.jpg');

        $samplings = array(
            array(1, 1, 1),
            array(2, 1, 1),
        );

        foreach ($samplings as $sampling) {
            $filename = $this->getTemporaryFilename(implode('-', $sampling) . '.jpg');
            $image->save($filename, array('jpeg_sampling_factors' => $sampling));
            $this->assertEquals($sampling, $this->getSamplingFactors($image));
        }
    }

    public function pasteWithAlphaProvider()
    {
        return array(
            array(0),
            array(25),
            array(50),
            array(75),
            array(100),
        );
    }

    /**
     * @dataProvider pasteWithAlphaProvider
     *
     * @param int $alpha
     */
    public function testPasteWithAlpha($alpha)
    {
        $rgb = new RGB();
        $imagine = $this->getImagine();
        $whiteImage = $imagine->create(new Box(3, 3), $rgb->color('FFF'));
        $blackImage = $imagine->create(new Box(1, 1), $rgb->color('000'));
        $whiteImage->paste($blackImage, new Point(1, 1), $alpha);
        $finalColor = $whiteImage->getColorAt(new Point(1, 1));
        $grayLevel = (int) (255 * (100 - $alpha) / 100);
        $expectedColor = $rgb->color(array($grayLevel, $grayLevel, $grayLevel));
        $this->assertColorSimilar($expectedColor, $finalColor, '', 1.74);
    }

    public function testPasteOutOfBoundaries()
    {
        $imagine = $this->getImagine();
        $palette = new RGB();
        $background = $imagine->create(new Box(10, 10), $palette->color('#ffffff'));
        $box = $imagine->create(new Box(10, 10), $palette->color('#000000'));

        $pasted = $background->copy()->paste($box, new PointSigned(-5, -5));
        $this->assertSame((string) $background->getSize(), (string) $pasted->getSize());
        $this->assertSame('#000000', (string) $pasted->getColorAt(new Point(0, 0)));
        $this->assertSame('#ffffff', (string) $pasted->getColorAt(new Point(9, 0)));
        $this->assertSame('#ffffff', (string) $pasted->getColorAt(new Point(9, 9)));
        $this->assertSame('#ffffff', (string) $pasted->getColorAt(new Point(0, 9)));

        $pasted = $background->copy()->paste($box, new PointSigned(5, -5));
        $this->assertSame((string) $background->getSize(), (string) $pasted->getSize());
        $this->assertSame('#ffffff', (string) $pasted->getColorAt(new Point(0, 0)));
        $this->assertSame('#000000', (string) $pasted->getColorAt(new Point(9, 0)));
        $this->assertSame('#ffffff', (string) $pasted->getColorAt(new Point(9, 9)));
        $this->assertSame('#ffffff', (string) $pasted->getColorAt(new Point(0, 9)));

        $pasted = $background->copy()->paste($box, new PointSigned(5, 5));
        $this->assertSame((string) $background->getSize(), (string) $pasted->getSize());
        $this->assertSame('#ffffff', (string) $pasted->getColorAt(new Point(0, 0)));
        $this->assertSame('#ffffff', (string) $pasted->getColorAt(new Point(9, 0)));
        $this->assertSame('#000000', (string) $pasted->getColorAt(new Point(9, 9)));
        $this->assertSame('#ffffff', (string) $pasted->getColorAt(new Point(0, 9)));

        $pasted = $background->copy()->paste($box, new PointSigned(-5, 5));
        $this->assertSame((string) $background->getSize(), (string) $pasted->getSize());
        $this->assertSame('#ffffff', (string) $pasted->getColorAt(new Point(0, 0)));
        $this->assertSame('#ffffff', (string) $pasted->getColorAt(new Point(9, 0)));
        $this->assertSame('#ffffff', (string) $pasted->getColorAt(new Point(9, 9)));
        $this->assertSame('#000000', (string) $pasted->getColorAt(new Point(0, 9)));
    }

    public function imageCompressionQualityProvider()
    {
        return array(
            array('jpg', array('jpeg_quality' => 0), array('jpeg_quality' => 100)),
            array('png', array('png_compression_level' => 9), array('png_compression_level' => 0)),
            array('webp', array('webp_quality' => 0), array('webp_quality' => 100)),
        );
    }

    /**
     * @dataProvider imageCompressionQualityProvider
     *
     * @param string $format
     * @param array $smallSizeOptions
     * @param array $bigSizeOptions
     */
    public function testSaveCompressionQuality($format, array $smallSizeOptions, array $bigSizeOptions)
    {
        $filenameSmall = $this->getTemporaryFilename('small.' . $format);
        $filenameBig = $this->getTemporaryFilename('big.' . $format);
        $image = $this->getImagine()->open(IMAGINE_TEST_FIXTURESFOLDER . '/xparent.gif');
        $image->copy()->save($filenameSmall, array('format' => $format) + $smallSizeOptions);
        $image->copy()->save($filenameBig, array('format' => $format) + $bigSizeOptions);
        $this->assertLessThan(filesize($filenameBig), filesize($filenameSmall));
    }

    public function testShouldFailOpeningAnInvalidImageFile()
    {
        $this->isGoingToThrowException('Imagine\Exception\RuntimeException');
        $this->getImagine()->open(__FILE__);
    }

    abstract protected function getImageResolution(ImageInterface $image);

    private function getMonoLayeredImage()
    {
        return $this->getImagine()->open(IMAGINE_TEST_FIXTURESFOLDER . '/google.png');
    }

    private function getMultiLayeredImage()
    {
        return $this->getImagine()->open(IMAGINE_TEST_FIXTURESFOLDER . '/cat.gif');
    }

    private function getInconsistentMultiLayeredImage()
    {
        return $this->getImagine()->open(IMAGINE_TEST_FIXTURESFOLDER . '/anima.gif');
    }

    /**
     * @return \Imagine\Image\ImagineInterface
     */
    abstract protected function getImagine();

    /**
     * @param ImageInterface $image
     *
     * @return array
     */
    abstract protected function getSamplingFactors(ImageInterface $image);
}
