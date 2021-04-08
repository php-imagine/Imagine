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
use Imagine\Test\Image\AbstractImagineTest;

/**
 * @group gd
 */
class ImagineTest extends AbstractImagineTest
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
        $this->markTestSkipped('GD does not support AVIF');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractImagineTest::testShouldOpenAHeicImage()
     */
    public function testShouldOpenAHeicImage()
    {
        $this->markTestSkipped('GD does not support HEIC');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractImagineTest::testShouldOpenAJxlImage()
     */
    public function testShouldOpenAJxlImage()
    {
        $this->markTestSkipped('GD does not support JXL');
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
}
