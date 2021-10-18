<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Test\Gmagick;

use Imagine\Gmagick\DriverInfo;
use Imagine\Gmagick\Imagine;
use Imagine\Image\ImageInterface;
use Imagine\Test\Image\AbstractImageTest;

/**
 * @group gmagick
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
        // disable GC while https://bugs.php.net/bug.php?id=63677 is still open
        // If GC enabled, Gmagick unit tests fail
        gc_disable();
    }

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
        return $image->getGmagick()->getimageresolution();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractImageTest::getSamplingFactors()
     */
    protected function getSamplingFactors(ImageInterface $image)
    {
        return $image->getGmagick()->getSamplingFactors();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractImageTest::testMask()
     */
    public function testMask()
    {
        $this->markTestSkipped('Temporarily skipped - see https://github.com/php-imagine/Imagine/issues/778');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractImageTest::testJpegSamplingFactors()
     */
    public function testJpegSamplingFactors()
    {
        $this->markTestSkipped('Temporarily skipped - see https://github.com/php-imagine/Imagine/issues/782');
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
                'Imagine\Image\Palette\CMYK',
                array(10, 10, 10),
            ),
            array(
                'Imagine\Image\Palette\CMYK',
                'Imagine\Image\Palette\RGB',
                array(10, 10, 10, 0),
            ),
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
            array('Imagine\Image\Palette\CMYK', array(10, 0, 0, 0)),
        );
    }

    /**
     * Alpha transparency is not supported by Gmagick.
     *
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractImageTest::pasteWithAlphaProvider()
     */
    public function pasteWithAlphaProvider()
    {
        return array(
            array(0),
            array(100),
        );
    }
}
