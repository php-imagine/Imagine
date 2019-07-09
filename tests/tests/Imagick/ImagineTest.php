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

use Imagine\Imagick\Imagine;
use Imagine\Test\Image\AbstractImagineTest;

/**
 * @group imagick
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

        if (!class_exists('Imagick')) {
            $this->markTestSkipped('Imagick is not installed');
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractImagineTest::testShouldOpenAWebPImage()
     */
    public function testShouldOpenAWebPImage()
    {
        if (!in_array('WEBP', \Imagick::queryFormats('WEBP'), true)) {
            $this->markTestSkipped('Imagick webp support is not enabled');
        }

        return parent::testShouldOpenAWebPImage();
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
