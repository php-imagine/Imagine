<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Test\Gmagick;

use Imagine\Gmagick\DriverInfo;
use Imagine\Gmagick\Image;
use Imagine\Gmagick\Imagine;
use Imagine\Gmagick\Layers;
use Imagine\Image\ImageInterface;
use Imagine\Image\Metadata\MetadataBag;
use Imagine\Image\Palette\RGB;
use Imagine\Test\Image\AbstractLayersTest;

/**
 * @group gmagick
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
            return new Image(new \Gmagick($path), new RGB(), new MetadataBag());
        } else {
            return new Image(new \Gmagick(), new RGB(), new MetadataBag());
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Test\Image\AbstractLayersTest::assertLayersEquals()
     */
    protected function assertLayersEquals(ImageInterface $expected, ImageInterface $actual)
    {
        $this->assertEquals($expected->getGmagick(), $actual->getGmagick());
    }

    public function testCount()
    {
        $this->checkGmagickMockable();
        $palette = new RGB();
        $resource = $this->getMockBuilder('\Gmagick')->getMock();

        $resource->expects($this->once())
            ->method('getnumberimages')
            ->will($this->returnValue(42));

        $layers = new Layers(new Image($resource, $palette, new MetadataBag()), $palette, $resource);

        $this->assertCount(42, $layers);
    }

    public function testGetLayer()
    {
        $this->checkGmagickMockable();
        $palette = new RGB();
        $resource = $this->getMockBuilder('\Gmagick')->getMock();

        $resource->expects($this->any())
            ->method('getnumberimages')
            ->will($this->returnValue(2));

        $layer = $this->getMockBuilder('\Gmagick')->getMock();

        $resource->expects($this->any())
            ->method('getimage')
            ->will($this->returnValue($layer));

        $layers = new Layers(new Image($resource, $palette, new MetadataBag()), $palette, $resource);

        foreach ($layers as $layer) {
            $this->assertInstanceOf('Imagine\Image\ImageInterface', $layer);
        }
    }

    /**
     * Check if the current Gmagick version is affected by the https://github.com/vitoc/gmagick/issues/55 bug.
     *
     * @throws \PHPUnit_Framework_SkippedTestError
     * @throws \PHPUnit\Framework\SkippedTestError
     * @throws \PHPUnit\Framework\SkippedWithMessageException
     *
     * @see https://github.com/vitoc/gmagick/issues/55
     */
    protected function checkGmagickMockable()
    {
        if (!method_exists('Gmagick', 'thresholdimage')) {
            return;
        }
        $method = new \ReflectionMethod('Gmagick', 'thresholdimage');
        $parameters = $method->getParameters();
        if (!isset($parameters[1])) {
            return;
        }
        try {
            $parameters[1]->getDefaultValue();
        } catch (\Error $x) {
            if ($x->getMessage() === 'Undefined constant "CHANNEL_DEFAULT"') {
                $this->markTestSkipped("Gmagick can't be mocked because of bug https://github.com/vitoc/gmagick/issues/55");
            }
        } catch (\Exception $x) {
            if ($x->getMessage() === 'Undefined constant "CHANNEL_DEFAULT"') {
                $this->markTestSkipped("Gmagick can't be mocked because of bug https://github.com/vitoc/gmagick/issues/55");
            }
        } catch (\Throwable $x) {
        }
    }
}
