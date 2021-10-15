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
use Imagine\Test\Image\AbstractImagineTest;

/**
 * @group gd
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
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractImagineTest::testShouldOpenAWebPImage()
     */
    public function testShouldOpenAWebPImage()
    {
        if (!function_exists('imagewebp')) {
            $this->markTestSkipped('GD webp support is not enabled');
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
        if (!function_exists('imageavif')) {
            $this->markTestSkipped('GD imageavif support is not enabled');
        }

        return parent::testShouldOpenAAvifImage();
    }

    /**
     * @group always-skipped
     *
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractImagineTest::testShouldOpenAHeicImage()
     */
    public function testShouldOpenAHeicImage()
    {
        $this->markTestSkipped('GD does not support HEIC');
    }

    /**
     * @group always-skipped
     *
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractImagineTest::testShouldOpenAJxlImage()
     */
    public function testShouldOpenAJxlImage()
    {
        $this->markTestSkipped('GD does not support JXL');
    }
}
