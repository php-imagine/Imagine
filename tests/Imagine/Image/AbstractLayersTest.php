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
use Imagine\Exception\InvalidArgumentException;
use Imagine\Exception\OutOfBoundsException;

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

    public function testLayerArrayAccess()
    {
        $resource = $this->getResource();
        $secondResource = $this->getResource();
        $thirdResource = $this->getResource();

        $layers = $this->getLayers($this->getImage($resource), $resource);

        $this->assertCount(1, $layers);

        $layers[] = $secondResource;

        $this->assertCount(2, $layers);
        $this->assertEquals($this->getImage($resource), $layers[0]);
        $this->assertEquals($this->getImage($secondResource), $layers[1]);

        $layers[1] = $thirdResource;

        $this->assertCount(2, $layers);
        $this->assertEquals($this->getImage($resource), $layers[0]);
        $this->assertEquals($this->getImage($thirdResource), $layers[1]);

        $layers[] = $secondResource;

        $this->assertCount(3, $layers);
        $this->assertEquals($this->getImage($resource), $layers[0]);
        $this->assertEquals($this->getImage($thirdResource), $layers[1]);
        $this->assertEquals($this->getImage($secondResource), $layers[2]);

        $this->assertTrue(isset($layers[2]));
        $this->assertTrue(isset($layers[1]));
        $this->assertTrue(isset($layers[0]));

        unset($layers[1]);

        $this->assertCount(2, $layers);
        $this->assertEquals($this->getImage($resource), $layers[0]);
        $this->assertEquals($this->getImage($secondResource), $layers[1]);

        $this->assertFalse(isset($layers[2]));
        $this->assertTrue(isset($layers[1]));
        $this->assertTrue(isset($layers[0]));
    }

    /**
     * @dataProvider provideInvalidArguments
     */
    public function testLayerArrayAccessInvalidArgumentExceptions($offset)
    {
        $resource = $this->getResource();
        $layers = $this->getLayers($this->getImage($resource), $resource);

        $secondResource = $this->getResource();

        try {
            $layers[$offset] = $secondResource;
            $this->fail('An exception should have been raised');
        } catch (InvalidArgumentException $e) {

        }
    }

    /**
     * @dataProvider provideOutOfBoundsArguments
     */
    public function testLayerArrayAccessOutOfBoundsExceptions($offset)
    {
        $resource = $this->getResource();
        $layers = $this->getLayers($this->getImage($resource), $resource);

        $secondResource = $this->getResource();

        try {
            $layers[$offset] = $secondResource;
            $this->fail('An exception should have been raised');
        } catch (OutOfBoundsException $e) {

        }
    }

    public function provideInvalidArguments()
    {
        return array(
            array('lambda'),
            array('0'),
            array('1'),
            array(1.0),
        );
    }

    public function provideOutOfBoundsArguments()
    {
        return array(
            array(-1),
            array(2),
        );
    }

    abstract protected function getResource();
    abstract protected function getImage($resource);
    abstract protected function getImagine();
    abstract protected function getLayers(ImageInterface $image, $resource);
}
