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
    /**
     * Testing `LayeredImageInterface#layers()` returns itself. If this is true, all other tests
     * should also cover `LayeredImageInterface`.
     */
    public function testImageLayersReturnsItself()
    {
        $red = new Color("#FF0000");
        $image = $this->getPolygonImage($red);
        
        $this->assertSame($image, $image->layers());
    }

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
        $redImage = $this->getPolygonImage($red);

        $blue = new Color("#0000FF");
        $blueImage = $this->getPolygonImage($blue);

        # Replace our first red image layer with the first blue image layer
        $redLayers = $redImage->layers();
        $blueLayers = $blueImage->layers();

        $redLayers[0] = $blueLayers[0];

        $this->assertEquals((string) $blue, (string) $redImage[0]->getColorAt(new Point(5,5)));

        # Merge all layers and "save" to PNG
        $redImage->layers()->merge();
        $redImage_string = $redImage->get("png");

        # Load in PNG
        $redImage = $this->getImagine()->load($redImage_string);

        # Assert the red image is now actually blue
        $this->assertEquals((string) $blue, (string) $redImage->getColorAt(new Point(5,5)));
    }

    public function testAdd()
    {
        $red = new Color("#FF0000");
        $redImage = $this->getPolygonImage($red);

        $blue = new Color("#0000FF");
        $blueImage = $this->getPolygonImage($blue);

        # Add a blue layer to our red image.
        $redLayers[] = $blueLayers[0];

        $this->assertEquals((string) $red, (string) $redImage[0]->getColorAt(new Point(5,5)));
        $this->assertEquals((string) $blue, (string) $redImage[1]->getColorAt(new Point(5,5)));
    }

    protected function getPolygonImage(Color $color)
    {
        $image = $this->getImagine()->create(new Box(20, 20), new Color('#FFFFFF'));
     
        foreach($image->layers() as $layer) {
            $layer->draw()
                ->polygon(
                    array(new Point(0, 0), new Point(0, 20), new Point(20, 20), new Point(20, 0)),
                    $color,
                    true
                );
        }

        return $image;
    }

    abstract protected function getImagine();
}
