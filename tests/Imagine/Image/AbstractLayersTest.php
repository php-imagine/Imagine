<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Image;

use Imagine\Image\Box;
use Imagine\Image\Color;
use Imagine\Image\Point;

abstract class AbstractLayersTest extends \PHPUnit_Framework_TestCase
{
    public function testMerge()
    {
        $image = $this->getImagine()->create(new Box(20, 20), new Color('#FFFFFF'));
        foreach($image->layers() as $layer) {
            $layer->draw()
                ->polygon(
                array(new Point(0, 0),new Point(0, 20),new Point(20, 20),new Point(20, 0)),
                new Color('#FF0000'),
                true
            );
        }
        $image->layers()->merge();

        $this->assertEquals('#ff0000', (string) $image->getColorAt(new Point(5,5)));
    }

    abstract protected function getImagine();
}
