<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Gmagick;

use Imagine\Image\AbstractImageTest;
use Imagine\Image\Color;
use Imagine\Image\ImageInterface;

class ImageTest extends AbstractImageTest
{
    protected function setUp()
    {
        parent::setUp();

        if (!class_exists('Gmagick')) {
            $this->markTestSkipped('Gmagick is not installed');
        }
    }

    protected function getImagine()
    {
        return new Imagine();
    }

    protected function supportMultipleLayers()
    {
        return true;
    }

    public function testColorHistogram()
    {
        $this->markTestSkipped('Test skipped while bug https://bugs.php.net/bug.php?id=63677 is not fixed');
    }
}
