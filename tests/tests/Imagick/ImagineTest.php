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
    protected function setUp()
    {
        parent::setUp();

        if (!class_exists('Imagick')) {
            $this->markTestSkipped('Imagick is not installed');
        }
    }

    protected function getImagine()
    {
        return new Imagine();
    }

    public function testShouldOpenAWebpImage()
    {
        if (!in_array('WEBP', \Imagick::queryFormats('WEBP'), true)) {
            $this->markTestSkipped('Imagick webp support is not enabled');
        }

        return parent::testShouldOpenAWebpImage();
    }
}
