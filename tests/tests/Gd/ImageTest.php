<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Test\Gd;

use Imagine\Gd\DriverInfo;
use Imagine\Gd\Imagine;
use Imagine\Image\ImageInterface;
use Imagine\Test\Image\AbstractImageTest;

/**
 * @group gd
 */
class ImageTest extends AbstractImageTest
{
    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Driver\InfoProvider::getDriverInfo()
     */
    public static function getDriverInfo($required = true)
    {
        return DriverInfo::get($required);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractImageTest::getImagine()
     */
    protected function getImagine()
    {
        return new Imagine();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractImageTest::getImageResolution()
     */
    protected function getImageResolution(ImageInterface $image)
    {
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractImageTest::getSamplingFactors()
     */
    protected function getSamplingFactors(ImageInterface $image)
    {
    }

    /**
     * @group always-skipped
     *
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractImageTest::testImageResolutionChange()
     */
    public function testImageResolutionChange()
    {
        $this->markTestSkipped('GD driver does not support resolution options');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractImageTest::provideFilters()
     */
    public function provideFilters()
    {
        return array(
            array(ImageInterface::FILTER_UNDEFINED),
        );
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractImageTest::providePalettes()
     */
    public function providePalettes()
    {
        return array(
            array('Imagine\Image\Palette\RGB', array(255, 0, 0)),
        );
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractImageTest::provideFromAndToPalettes()
     */
    public function provideFromAndToPalettes()
    {
        return array(
            array(
                'Imagine\Image\Palette\RGB',
                'Imagine\Image\Palette\RGB',
                array(10, 10, 10),
            ),
        );
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractImageTest::testProfile()
     */
    public function testProfile()
    {
        $this->isGoingToThrowException('Imagine\Exception\RuntimeException');
        parent::testProfile();
    }

    /**
     * @group always-skipped
     *
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractImageTest::testPaletteIsGrayIfGrayImage()
     */
    public function testPaletteIsGrayIfGrayImage()
    {
        $this->markTestSkipped('GD driver does not support Gray colorspace');
    }

    /**
     * @group always-skipped
     *
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractImageTest::testPaletteIsCMYKIfCMYKImage()
     */
    public function testPaletteIsCMYKIfCMYKImage()
    {
        $this->markTestSkipped('GD driver does not recognize CMYK images properly');
    }

    /**
     * @group always-skipped
     *
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractImageTest::testGetColorAtCMYK()
     */
    public function testGetColorAtCMYK()
    {
        $this->markTestSkipped('GD driver does not recognize CMYK images properly');
    }

    /**
     * @group always-skipped
     *
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractImageTest::testChangeColorSpaceAndStripImage()
     */
    public function testChangeColorSpaceAndStripImage()
    {
        $this->markTestSkipped('GD driver does not support ICC profiles');
    }

    /**
     * @group always-skipped
     *
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractImageTest::testStripImageWithInvalidProfile()
     */
    public function testStripImageWithInvalidProfile()
    {
        $this->markTestSkipped('GD driver does not support ICC profiles');
    }

    /**
     * @group always-skipped
     *
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractImageTest::testStripGBRImageHasGoodColors()
     */
    public function testStripGBRImageHasGoodColors()
    {
        $this->markTestSkipped('GD driver does not support ICC profiles');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractImageTest::testRotateWithNoBackgroundColor()
     */
    public function testRotateWithNoBackgroundColor()
    {
        $vFrom = '5.5';
        $vTo = '7.1.12';
        $vNow = PHP_VERSION;
        if (version_compare($vNow, $vFrom, '>=') && version_compare($vNow, $vTo, '<')) {
            // see https://bugs.php.net/bug.php?id=65148
            $this->markTestSkipped("Skipped because PHP is affected by bug #65148 from version {$vFrom} to version {$vTo} (current version: {$vNow}).");
        }

        parent::testRotateWithNoBackgroundColor();
    }

    /**
     * @group always-skipped
     *
     * @dataProvider provideVariousSources
     *
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractImageTest::testResolutionOnSave()
     */
    public function testResolutionOnSave($source)
    {
        $this->markTestSkipped('GD driver only supports 72 dpi resolution');
    }

    /**
     * @dataProvider imageCompressionQualityProvider
     *
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractImageTest::testSaveCompressionQuality()
     */
    public function testSaveCompressionQuality($format, array $smallSizeOptions, array $bigSizeOptions)
    {
        if ($format === 'webp' && !function_exists('imagewebp')) {
            $this->markTestSkipped('GD webp support is not enabled');
        }
        if ($format === 'avif' && !function_exists('imageavif')) {
            $this->markTestSkipped('GD avif support is not enabled');
        }
        if ($format === 'heic' || $format === 'jxl') {
            $this->markTestSkipped('GD does not support ' . strtoupper($format));
        }

        return parent::testSaveCompressionQuality($format, $smallSizeOptions, $bigSizeOptions);
    }

    /**
     * @group always-skipped
     *
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractImageTest::testJpegSamplingFactors()
     */
    public function testJpegSamplingFactors()
    {
        $this->markTestSkipped('GD driver does not support JPEG sampling factors');
    }

    /**
     * @group always-skipped
     *
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractImageTest::testCountAMultiLayeredImage()
     */
    public function testCountAMultiLayeredImage()
    {
        $this->markTestSkipped('GD driver does not support multiple layers');
    }

    /**
     * @group always-skipped
     *
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractImageTest::testResizeAnimatedGifResizeResult()
     */
    public function testResizeAnimatedGifResizeResult()
    {
        $this->markTestSkipped('GD driver does not support multiple layers');
    }
}
