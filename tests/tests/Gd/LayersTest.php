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
     * @see \Imagine\Test\ImagineTestCaseBase::setUpBase()
     */
    protected function setUpBase()
    {
        parent::setUpBase();

        if (!function_exists('gd_info')) {
            $this->markTestSkipped('Gd not installed');
        }
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

    public function testLayerArrayAccess()
    {
        $image = $this->getImage(IMAGINE_TEST_FIXTURESFOLDER . '/pink.gif');
        $layers = $image->layers();

        $this->assertLayersEquals($image, $layers[0]);
        $this->assertTrue(isset($layers[0]));
    }

    public function testLayerAddGetSetRemove()
    {
        $image = $this->getImage(IMAGINE_TEST_FIXTURESFOLDER . '/pink.gif');
        $layers = $image->layers();

        $this->assertLayersEquals($image, $layers->get(0));
        $this->assertTrue($layers->has(0));
    }

    /**
     * @group always-skipped
     *
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractLayersTest::testLayerArrayAccessInvalidArgumentExceptions()
     */
    public function testLayerArrayAccessInvalidArgumentExceptions($offset = null)
    {
        $this->markTestSkipped('GD driver does not fully support layers array access');
    }

    /**
     * @group always-skipped
     *
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractLayersTest::testLayerArrayAccessOutOfBoundsExceptions()
     */
    public function testLayerArrayAccessOutOfBoundsExceptions($offset = null)
    {
        $this->markTestSkipped('GD driver does not fully support layers array access');
    }

    /**
     * @group always-skipped
     *
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractLayersTest::testAnimateEmpty()
     */
    public function testAnimateEmpty()
    {
        $this->markTestSkipped('GD driver does not support animated gifs');
    }

    /**
     * @group always-skipped
     *
     * @dataProvider provideAnimationParameters
     *
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractLayersTest::testAnimateWithParameters()
     */
    public function testAnimateWithParameters($delay, $loops)
    {
        $this->markTestSkipped('GD driver does not support animated gifs');
    }

    /**
     * @group always-skipped
     *
     * @dataProvider provideAnimationParameters
     *
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractLayersTest::testAnimateWithWrongParameters()
     */
    public function testAnimateWithWrongParameters($delay, $loops)
    {
        $this->markTestSkipped('GD driver does not support animated gifs');
    }

    public function getImage($path = null)
    {
        return new Image(imagecreatetruecolor(10, 10), new RGB(), new MetadataBag());
    }

    public function getLayers(ImageInterface $image, $resource)
    {
        return new Layers($image, new RGB(), $resource);
    }

    public function getImagine()
    {
        return new Imagine();
    }

    protected function assertLayersEquals($expected, $actual)
    {
        $this->assertEquals($expected->getGdResource(), $actual->getGdResource());
    }
}
