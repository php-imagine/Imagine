<?php

namespace Imagine\Test\Functional;

use Imagine\Image\Color;
use Imagine\Image\Point;
use Imagine\Gd\Imagine;
use Imagine\Exception\RuntimeException;

class GdTransparentGifHandlingTest extends \PHPUnit_Framework_TestCase
{
    private function getImagine()
    {
        try {
            $imagine = new Imagine();
        } catch (RuntimeException $e) {
            $this->markTestSkipped($e->getMessage());
        }

        return $imagine;
    }

    public function testShouldResize()
    {
        $imagine = $this->getImagine();
        $new     = sys_get_temp_dir()."/sample.jpeg";

        $image = $imagine->open('tests/Imagine/Fixtures/xparent.gif');
        $size  = $image->getSize()->scale(0.5);

        $image
            ->resize($size)
        ;

        $imagine
            ->create($size, new Color("fff", 100))
            ->paste($image, new Point(0, 0))
            ->save($new)
        ;

        unlink($new);
    }
}
