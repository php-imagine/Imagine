<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Test\Imagick;

use Imagine\Image\ImageInterface;
use Imagine\Image\Metadata\MetadataBag;
use Imagine\Image\Palette\RGB;
use Imagine\Imagick\DriverInfo;
use Imagine\Imagick\Image;
use Imagine\Imagick\Imagine;
use Imagine\Imagick\Layers;
use Imagine\Test\Image\AbstractLayersTest;

/**
 * @group imagick
 */
class LayersTest extends AbstractLayersTest
{
    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Driver\InfoProvider::getDriverInfo()
     */
    public static function getDriverInfo($required = true)
    {
        return DriverInfo::get($required);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractLayersTest::getImagine()
     */
    protected function getImagine()
    {
        return new Imagine();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractLayersTest::getImage()
     */
    protected function getImage($path = null)
    {
        if ($path) {
            return new Image(new \Imagick($path), new RGB(), new MetadataBag());
        } else {
            return new Image(new \Imagick(), new RGB(), new MetadataBag());
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractLayersTest::assertLayersEquals()
     */
    protected function assertLayersEquals(ImageInterface $expected, ImageInterface $actual)
    {
        $this->assertEquals($expected->getImagick(), $actual->getImagick());
    }

    public function testCount()
    {
        $palette = new RGB();
        $resource = $this->getMockBuilder('\Imagick')->getMock();

        $resource->expects($this->atLeastOnce())
            ->method('getNumberImages')
            ->will($this->returnValue(42));

        $layers = new Layers(new Image($resource, $palette, new MetadataBag()), $palette, $resource);

        $this->assertCount(42, $layers);
    }

    public function testWebpFormatIsAllowedAsAnimatedFormat()
    {
        $image = $this->getImagine()->open(IMAGINE_TEST_FIXTURESFOLDER . '/anima3.gif');
        $originalDelayInTicks = $image->layers()->get(0)->getImagick()->getImageDelay();
        $image->layers()->animate('webp', (int) (1000 + $originalDelayInTicks * 1000 / 20), 0);
        $this->assertSame('webp', $image->getImagick()->getFormat());
        $newDelayInTicks = $image->layers()->get(0)->getImagick()->getImageDelay();
        $this->assertNotEquals($originalDelayInTicks, $newDelayInTicks);
    }

    public function testGetLayer()
    {
        $palette = new RGB();
        $resource = $this->getMockBuilder('\Imagick')->getMock();

        $resource->expects($this->any())
            ->method('getNumberImages')
            ->will($this->returnValue(2));

        $layer = $this->getMockBuilder('\Imagick')->getMock();

        $resource->expects($this->any())
            ->method('getImage')
            ->will($this->returnValue($layer));

        $layers = new Layers(new Image($resource, $palette, new MetadataBag()), $palette, $resource);

        foreach ($layers as $layer) {
            $this->assertInstanceOf('Imagine\Image\ImageInterface', $layer);
        }
    }

    public function testCoalesce()
    {
        $width = null;
        $height = null;

        $resource = new \Imagick();
        $palette = new RGB();
        $resource->newImage(20, 10, new \ImagickPixel('black'));
        $resource->newImage(10, 10, new \ImagickPixel('black'));

        $layers = new Layers(new Image($resource, $palette, new MetadataBag()), $palette, $resource);
        $layers->coalesce();

        foreach ($layers as $layer) {
            $size = $layer->getSize();

            if ($width === null) {
                $width = $size->getWidth();
            } else {
                $this->assertEquals($width, $size->getWidth());
            }

            if ($height === null) {
                $height = $size->getHeight();
            } else {
                $this->assertEquals($height, $size->getHeight());
            }
        }
    }
}
