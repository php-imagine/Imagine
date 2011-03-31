<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Imagick;

use Imagine\AbstractImageTest;
use Imagine\Image\Color;
use Imagine\ImageInterface;

class ImageTest extends AbstractImageTest
{
    protected function getImagine()
    {
        return new Imagine();
    }
}
