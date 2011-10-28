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

use Imagine\Image\AbstractImageTest;
use Imagine\Image\Color;
use Imagine\Image\ImageInterface;

class ImageTest extends AbstractImageTest
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

    public function testImageResizeUsesProperMethodBasedOnInputAndOutputSizes()
    {
        $imagine = $this->getImagine();

        $image = $imagine->open('tests/Imagine/Fixtures/resize/210-design-19933.jpg');

        $image
            ->resize(new \Imagine\Image\Box(1500, 750))
            ->save('tests/Imagine/Fixtures/resize/large.png')
        ;

        $image
            ->resize(new \Imagine\Image\Box(100, 50))
            ->save('tests/Imagine/Fixtures/resize/small.png')
        ;

        unlink('tests/Imagine/Fixtures/resize/large.png');
        unlink('tests/Imagine/Fixtures/resize/small.png');
    }
}
