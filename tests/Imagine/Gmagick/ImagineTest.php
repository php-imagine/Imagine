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

use Imagine\AbstractImagineTest;

class ImagineTest extends AbstractImagineTest
{
    protected function setUp()
    {
        if (!class_exists('Gmagick')) {
            $this->markTestSkipped('Gmagick is not installed');
        }
    }

    protected function getImagine()
    {
        return new Imagine();
    }
}
