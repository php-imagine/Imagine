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

use Imagine\Image\AbstractImagineTest;
use Imagine\Image\Box;

class ImagineTest extends AbstractImagineTest
{
    protected function setUp()
    {
        parent::setUp();

        if (!class_exists('Gmagick')) {
            $this->markTestSkipped('Gmagick is not installed');
        }
    }

    protected function getEstimatedFontBox()
    {
        return new Box(117, 55);
    }

    protected function getImagine()
    {
        return new Imagine();
    }
    
    protected function isFontTestSupported()
    {
        return true;
    }
}
