<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Gd;

class ImageTest extends \PHPUnit_Framework_TestCase
{
    public function testRotate()
    {
        $image = new FileImage('tests/Imagine/Fixtures/google.png');

        $image->paste(
                $image->copy()
                    ->resize($image->getWidth() / 2, $image->getHeight() / 2)
                    ->flipVertically(),
                $image->getWidth() / 2 - 1,
                $image->getHeight() / 2 - 1)
            ->save('tests/Imagine/Fixtures/clone.jpg', array('quality' => 100));

        unset($image);
    }
}
