<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Test\Imagick;

use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\Metadata\MetadataBag;
use Imagine\Image\Palette\CMYK;
use Imagine\Image\Palette\RGB;
use Imagine\Image\Point;
use Imagine\Imagick\Image;
use Imagine\Imagick\Imagine;
use Imagine\Test\Image\AbstractImageTest;

/**
 * @group imagick
 */
class ImageTest extends AbstractImageTest
{
    /**
     * @dataProvider provideFromAndToPalettes
     *
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractImageTest::testUsePalette()
     */
    public function testUsePalette($from, $to, $color)
    {
        if ($from === 'Imagine\\Image\\Palette\\Grayscale' && $to === 'Imagine\\Image\\Palette\\RGB') {
            $this->markTestSkipped('Temporarily skipped - see https://github.com/php-imagine/Imagine/issues/780');
        }
        if ($from === 'Imagine\\Image\\Palette\\Grayscale' && $to === 'Imagine\Image\Palette\CMYK') {
            $this->markTestSkipped('Temporarily skipped - see https://github.com/php-imagine/Imagine/issues/780');
        }
        parent::testUsePalette($from, $to, $color);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractImageTest::testMask()
     */
    public function testMask()
    {
        $this->markTestSkipped('Temporarily skipped - see https://github.com/php-imagine/Imagine/issues/781');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractImageTest::testGetColorAtGrayScale()
     */
    public function testGetColorAtGrayScale()
    {
        $this->markTestSkipped('Temporarily skipped - see https://github.com/php-imagine/Imagine/issues/783');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractImageTest::testGetColorAtOpaque()
     */
    public function testGetColorAtOpaque()
    {
        $this->markTestSkipped('Temporarily skipped - see https://github.com/php-imagine/Imagine/issues/784');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractImageTest::testStripGBRImageHasGoodColors()
     */
    public function testStripGBRImageHasGoodColors()
    {
        $this->markTestSkipped('Temporarily skipped - see https://github.com/php-imagine/Imagine/issues/785');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Test\ImagineTestCaseBase::setUpBase()
     */
    protected function setUpBase()
    {
        parent::setUpBase();

        if (!class_exists('Imagick')) {
            $this->markTestSkipped('Imagick is not installed');
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Test\ImagineTestCaseBase::tearDownBase()
     */
    protected function tearDownBase()
    {
        if (class_exists('Imagick')) {
            $prop = new \ReflectionProperty('Imagine\Imagick\Image', 'supportsColorspaceConversion');
            $prop->setAccessible(true);
            $prop->setValue(null);
        }

        parent::tearDownBase();
    }

    protected function getImagine()
    {
        return new Imagine();
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testImageResizeUsesProperMethodBasedOnInputAndOutputSizes()
    {
        $imagine = $this->getImagine();

        $image = $imagine->open(IMAGINE_TEST_FIXTURESFOLDER . '/resize/210-design-19933.jpg');

        $filenameLarge = $this->getTemporaryFilename('large.png');
        $image
            ->resize(new Box(1500, 750))
            ->save($filenameLarge)
        ;

        $filenameLarge = $this->getTemporaryFilename('small.png');
        $image
            ->resize(new Box(100, 50))
            ->save($filenameLarge)
        ;
    }

    public function testAnimatedGifResize()
    {
        $imagine = $this->getImagine();
        $image = $imagine->open(IMAGINE_TEST_FIXTURESFOLDER . '/anima3.gif');
        $filename = $this->getTemporaryFilename('.gif');
        $image
            ->resize(new Box(150, 100))
            ->save($filename, array('animated' => true))
        ;
        $this->assertImageEquals(
            $imagine->open(IMAGINE_TEST_FIXTURESFOLDER . '/resize/anima3-150x100.gif'),
            $imagine->open($filename)
        );
    }

    /**
     * Older imagemagick versions does not support colorspace conversion.
     *
     * @doesNotPerformAssertions
     *
     * @return \Imagine\Imagick\Image
     */
    public function testOlderImageMagickDoesNotAffectColorspaceUsageOnConstruct()
    {
        $palette = new CMYK();
        $imagick = $this->getMockBuilder('\Imagick')->getMock();
        $imagick->expects($this->any())
            ->method('setColorspace')
            ->will($this->throwException(new \RuntimeException('Method not supported')));

        $prop = new \ReflectionProperty('Imagine\Imagick\Image', 'supportsColorspaceConversion');
        $prop->setAccessible(true);
        $prop->setValue(false);

        return new Image($imagick, $palette, new MetadataBag());
    }

    /**
     * @depends testOlderImageMagickDoesNotAffectColorspaceUsageOnConstruct
     *
     * @param mixed $image
     */
    public function testOlderImageMagickDoesNotAffectColorspaceUsageOnPaletteChange($image)
    {
        $this->isGoingToThrowException('Imagine\Exception\RuntimeException', 'Your version of Imagick does not support colorspace conversions.');
        $image->usePalette(new RGB());
    }

    public function testAnimatedGifCrop()
    {
        $imagine = $this->getImagine();
        $image = $imagine->open(IMAGINE_TEST_FIXTURESFOLDER . '/anima3.gif');
        $filename = $this->getTemporaryFilename('.gif');
        $image
            ->crop(
                new Point(0, 0),
                new Box(150, 100)
            )
            ->save($filename, array('animated' => true))
        ;
        $this->assertImageEquals(
            $imagine->open(IMAGINE_TEST_FIXTURESFOLDER . '/crop/anima3-topleft.gif'),
            $imagine->open($filename)
        );
    }

    public function testOptimize()
    {
        $imagine = $this->getImagine();
        $rgb = new RGB();
        $image = $imagine->create(new Box(100, 100), $rgb->color('#fff'));
        $blackFrame = $imagine->create($image->getSize(), $rgb->color('#000'));
        $image->layers()->add(clone $blackFrame)->add(clone $blackFrame)->add(clone $blackFrame)->add(clone $blackFrame);
        $originalFilename = $this->getTemporaryFilename('not-optimized.gif');
        $image->save($originalFilename, array('animated' => true, 'optimize' => false));
        $originalSize = filesize($originalFilename);
        $optimizedFilename = $this->getTemporaryFilename('optimized.gif');
        $image->save($optimizedFilename, array('animated' => true, 'optimize' => true));
        $optimizedSize = filesize($optimizedFilename);
        $this->assertLessThan($originalSize, $optimizedSize);
    }

    /**
     * @dataProvider imageCompressionQualityProvider
     *
     * {@inheritdoc}
     */
    public function testSaveCompressionQuality($format, array $smallSizeOptions, array $bigSizeOptions)
    {
        if (in_array($format, array('webp', 'avif', 'heic', 'jxl'), true) && !in_array(strtoupper($format), \Imagick::queryFormats(strtoupper($format)), true)) {
            $this->markTestSkipped('Imagick ' . $format . ' support is not enabled');
        }

        return parent::testSaveCompressionQuality($format, $smallSizeOptions, $bigSizeOptions);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testOptimizeWithDifferentFrameSizes()
    {
        $imagine = $this->getImagine();
        $rgb = new RGB();
        $image = $imagine->create(new Box(10, 10), $rgb->color('#fff'));
        $image->layers()->add($imagine->create($image->getSize()->scale(2)), $rgb->color('#fff'));
        $filename = $this->getTemporaryFilename('.gif');
        $image->save($filename, array('animated' => true, 'optimize' => true));
    }

    protected function getImageResolution(ImageInterface $image)
    {
        return $image->getImagick()->getImageResolution();
    }

    protected function getSamplingFactors(ImageInterface $image)
    {
        return $image->getImagick()->getSamplingFactors();
    }
}
