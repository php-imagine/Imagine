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
        $red = new Color("#FF0000");
        $image = $this->getPolygonImage($red);

        $image->layers()->merge();
        $this->assertEquals((string) $red, (string) $image->getColorAt(new Point(5,5)));
    }

    public function testReplace()
    {
        $red = new Color("#FF0000");
        $red_image = $this->getPolygonImage($red);

        $blue = new Color("#0000FF");
        $blue_image = $this->getPolygonImage($blue);

        # Replace our first red image layer with the first blue image layer
        $red_layers = $red_image->layers();

        $blue_layers = $blue_image->layers();
        $blue_layers->rewind();

        $red_layers->replace(0, $blue_layers->current());

        # Merge all layers and "save" to PNG
        $red_image->layers()->merge();
        $red_image_string = $red_image->get("png");

        # Load in PNG
        $red_image = $this->getImagine()->load($red_image_string);

        # Assert the red image is now actually blue
        $this->assertEquals((string) $blue, (string) $red_image->getColorAt(new Point(5,5)));
    }

    protected function getPolygonImage(Color $color)
    {
        $image = $this->getImagine()->create(new Box(20, 20), new Color('#FFFFFF'));
     
        foreach($image->layers() as $layer) {
            $layer->draw()
                ->polygon(
                array(new Point(0, 0),new Point(0, 20),new Point(20, 20),new Point(20, 0)),
                $color,
                true
            );
        }

        return $image;
    }

    abstract protected function getImagine();
}
