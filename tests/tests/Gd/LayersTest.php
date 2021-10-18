<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Test\Gd;

use Imagine\Gd\DriverInfo;
use Imagine\Gd\Image;
use Imagine\Gd\Imagine;
use Imagine\Gd\Layers;
use Imagine\Image\ImageInterface;
use Imagine\Image\Metadata\MetadataBag;
use Imagine\Image\Palette\RGB;
use Imagine\Test\Image\AbstractLayersTest;

/**
 * @group gd
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
        return new Image(imagecreatetruecolor(10, 10), new RGB(), new MetadataBag());
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractLayersTest::assertLayersEquals()
     */
    protected function assertLayersEquals(ImageInterface $expected, ImageInterface $actual)
    {
        $this->assertEquals($expected->getGdResource(), $actual->getGdResource());
    }

    public function testCount()
    {
        $resource = imagecreate(20, 20);
        $palette = $this->getMockBuilder('Imagine\Image\Palette\PaletteInterface')->getMock();
        $layers = new Layers(new Image($resource, $palette, new MetadataBag()), $palette, $resource);

        $this->assertCount(1, $layers);
    }

    public function testGetLayer()
    {
        $resource = imagecreate(20, 20);
        $palette = $this->getMockBuilder('Imagine\Image\Palette\PaletteInterface')->getMock();
        $layers = new Layers(new Image($resource, $palette, new MetadataBag()), $palette, $resource);

        foreach ($layers as $layer) {
            $this->assertInstanceOf('Imagine\Image\ImageInterface', $layer);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractLayersTest::testLayerArrayAccess()
     */
    public function testLayerArrayAccess()
    {
        $image = $this->getImage(IMAGINE_TEST_FIXTURESFOLDER . '/pink.gif');
        $layers = $image->layers();

        $this->assertLayersEquals($image, $layers[0]);
        $this->assertTrue(isset($layers[0]));
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractLayersTest::testLayerAddGetSetRemove()
     */
    public function testLayerAddGetSetRemove()
    {
        $image = $this->getImage(IMAGINE_TEST_FIXTURESFOLDER . '/pink.gif');
        $layers = $image->layers();

        $this->assertLayersEquals($image, $layers->get(0));
        $this->assertTrue($layers->has(0));
    }
}
