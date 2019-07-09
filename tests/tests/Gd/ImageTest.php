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
     * @see \Imagine\Test\ImagineTestCaseBase::setUpBase()
     */
    protected function setUpBase()
    {
        parent::setUpBase();

        if (!function_exists('gd_info')) {
            $this->markTestSkipped('Gd not installed');
        }
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

    public function provideFilters()
    {
        return array(
            array(ImageInterface::FILTER_UNDEFINED),
        );
    }

    public function providePalettes()
    {
        return array(
            array('Imagine\Image\Palette\RGB', array(255, 0, 0)),
        );
    }

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

    protected function getImagine()
    {
        return new Imagine();
    }

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

    protected function getImageResolution(ImageInterface $image)
    {
    }

    protected function getSamplingFactors(ImageInterface $image)
    {
    }
}
