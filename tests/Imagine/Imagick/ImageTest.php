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

use Imagine\Color;

use Imagine\ImageInterface;

class ImageTest extends \PHPUnit_Framework_TestCase
{
    public function testRotate()
    {
        $factory = new Imagine();

        $image = $factory->open('tests/Imagine/Fixtures/google.png');

        $image->paste(
                $image->copy()
                    ->resize($image->getWidth() / 2, $image->getHeight() / 2)
                    ->flipVertically(),
                $image->getWidth() / 2 - 1,
                $image->getHeight() / 2 - 1)
            ->save('tests/Imagine/Fixtures/clone.jpg', array('quality' => 100));

        unset($image);

        $image = $factory->open('tests/Imagine/Fixtures/clone.jpg');

        $this->assertEquals(364, $image->getWidth());
        $this->assertEquals(126, $image->getHeight());

        unlink('tests/Imagine/Fixtures/clone.jpg');
    }

    public function testThumbnailGeneration()
    {
        $factory = new Imagine();

        $image = $factory->open('tests/Imagine/Fixtures/google.png');

        $image->thumbnail(50, 50, ImageInterface::THUMBNAIL_INSET, new Color('fff'))
            ->save('tests/Imagine/Fixtures/inset.png', array('quality' => 9));

        $image->thumbnail(50, 50, ImageInterface::THUMBNAIL_OUTBOUND)
            ->save('tests/Imagine/Fixtures/outbound.png', array('quality' => 9));

        $thumbnail = $factory->open('tests/Imagine/Fixtures/inset.png');
        $this->assertEquals(50, $thumbnail->getWidth());
        $this->assertEquals(50, $thumbnail->getHeight());
        unlink('tests/Imagine/Fixtures/inset.png');

        $thumbnail = $factory->open('tests/Imagine/Fixtures/outbound.png');
        $this->assertEquals(50, $thumbnail->getWidth());
        $this->assertEquals(50, $thumbnail->getHeight());
        unlink('tests/Imagine/Fixtures/outbound.png');
    }
}
