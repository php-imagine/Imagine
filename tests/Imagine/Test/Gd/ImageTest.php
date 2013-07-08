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
use Imagine\Test\Image\AbstractImageTest;
use Imagine\Image\ImageInterface;

class ImageTest extends AbstractImageTest
{
    protected function setUp()
    {
        parent::setUp();

        if (!function_exists('gd_info')) {
            $this->markTestSkipped('Gd not installed');
        }
    }

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

    protected function getImagine()
    {
        return new Imagine();
    }

    protected function supportMultipleLayers()
    {
        return false;
    }

    public function testRotateWithNoBackgroundColor()
    {
        if (version_compare(PHP_VERSION, '5.5', '>=')) {
            // see https://bugs.php.net/bug.php?id=65148
            $this->markTestSkipped('Disabling test while bug #65148 is open');
        }

        parent::testRotateWithNoBackgroundColor();
    }
}
