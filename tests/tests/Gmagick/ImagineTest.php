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
use Imagine\Test\Image\AbstractImagineTest;

/**
 * @group gmagick
 */
class ImagineTest extends AbstractImagineTest
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
     * @see \Imagine\Test\Image\AbstractImagineTest::getImagine()
     */
    protected function getImagine()
    {
        return new Imagine();
    }

    /**
     * @group always-skipped
     *
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractImagineTest::testCreateAlphaPrecision()
     */
    public function testCreateAlphaPrecision()
    {
        $this->markTestSkipped('Alpha transparency is not supported by Gmagick');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractImagineTest::testShouldOpenAWebPImage()
     */
    public function testShouldOpenAWebPImage()
    {
        $gmagick = new \Gmagick();
        if (!in_array('WEBP', $gmagick->queryformats('WEBP'), true)) {
            $this->markTestSkipped('Gmagick webp support is not enabled');
        }

        return parent::testShouldOpenAWebPImage();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractImagineTest::testShouldOpenAAvifImage()
     */
    public function testShouldOpenAAvifImage()
    {
        $gmagick = new \Gmagick();
        if (!in_array('AVIF', $gmagick->queryformats('AVIF'), true)) {
            $this->markTestSkipped('Gmagick AVIF support is not enabled');
        }

        return parent::testShouldOpenAAvifImage();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractImagineTest::testShouldOpenAHeicImage()
     */
    public function testShouldOpenAHeicImage()
    {
        $gmagick = new \Gmagick();
        if (!in_array('HEIC', $gmagick->queryformats('HEIC'), true)) {
            $this->markTestSkipped('Gmagick HEIC support is not enabled');
        }

        return parent::testShouldOpenAHeicImage();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractImagineTest::testShouldOpenAJxlImage()
     */
    public function testShouldOpenAJxlImage()
    {
        $gmagick = new \Gmagick();
        if (!in_array('JXL', $gmagick->queryformats('JXL'), true)) {
            $this->markTestSkipped('Gmagick JXL support is not enabled');
        }

        return parent::testShouldOpenAJxlImage();
    }
}
